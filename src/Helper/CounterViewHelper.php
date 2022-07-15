<?php

namespace ArmelWanes\Crudify\Helper;

use App\Entity\CounterView;
use App\Repository\CounterViewRepository;
use Carbon\Carbon;
use DateTime;
use DeviceDetector\DeviceDetector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CounterViewHelper
{
    protected $em;
    protected $counterViewRepository;

    public function __construct(EntityManagerInterface $em, CounterViewRepository $counterViewRepository)
    {
        $this->em = $em;
        $this->counterViewRepository = $counterViewRepository;
    }

    public function hasView(Request $request)
    {
        $ipdat = @json_decode(file_get_contents(
            "http://www.geoplugin.net/json.gp?ip=" . $request->getClientIp()));
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $dd = new DeviceDetector($userAgent);
        $dd->parse();
        if ($this->canIncrementView($request->getClientIp(), $request->getUri())) {
            $cv = new CounterView();
            $cv->setVisitedDate(new DateTime());
            $cv->setUrl($request->getUri());
            $cv->setVisitorIp($request->getClientIp());
            $cv->setCountry($ipdat->geoplugin_countryName);
            $cv->setCity($ipdat->geoplugin_city);
            $cv->setContinent($ipdat->geoplugin_continentName);
            $cv->setLattitude($ipdat->geoplugin_latitude);
            $cv->setLongitude($ipdat->geoplugin_longitude);
            $cv->setCurrencySymbol($ipdat->geoplugin_currencySymbol);
            $cv->setCurrencyCode($ipdat->geoplugin_currencyCode);
            $cv->setTimezone($ipdat->geoplugin_timezone);
            $cv->setBrowserInfo($dd->getClient());
            $cv->setBrowser($dd->getClient()['name']);
            $cv->setOsInfo($dd->getOs());
            $cv->setDevice($dd->getDeviceName());
            $cv->setBrand($dd->getBrandName());
            $cv->setModel($dd->getModel());
            $this->em->persist($cv);
            $this->em->flush();
        }
    }

    public function canIncrementView($ip, $url)
    {
        $data = $this->counterViewRepository->getIpLastVisit($ip, $url);

        if ($data != null) {
            $convertStringToDate = new Datetime($data[1]);
            if (!$this->isRequestInTime($convertStringToDate)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
    // si supérieur à 10min, retourne false
    // sinon retourne false
    private function isRequestInTime(Datetime $visitedDate = null)
    {
        if ($visitedDate === null) {
            return false;
        }
        $now = new DateTime();
        $interval = $now->getTimestamp() - $visitedDate->getTimestamp();

        $daySeconds = 60 * 1;
        $response = $interval > $daySeconds ? false : $reponse = true;
        return $response;
    }

    public function countViewWeekParDay()
    {
        return $this->counterViewRepository->countViewWeekParDay();
    }

    public function dashboard($query, $date1, $date2)
    {
        if ($query == "") {
            $data['view'] = $this->WeekViewCount();
            $data['datedeb'] = Carbon::now()->startOfWeek();
            $data['datefin'] = Carbon::now()->endOfWeek();
            $data['country'] = $this->countViewPerCountryPerWeek();
            $data['browser'] = $this->countViewPerBrowserPerWeek();
            $data['device'] = $this->countViewPerDevicePerWeek();
        }
        switch ($query) {
            case 'csemaine':
                $data['view'] = $this->WeekViewCount();
                $data['datedeb'] = Carbon::now()->startOfWeek();
                $data['datefin'] = Carbon::now()->endOfWeek();
                $data['country'] = $this->countViewPerCountryPerWeek();
                $data['browser'] = $this->countViewPerBrowserPerWeek();
                $data['device'] = $this->countViewPerDevicePerWeek();
                break;
            case 'dsemaine':
                $data['view'] = $this->LastWeekViewCount();
                $data['datedeb'] = Carbon::now()->subWeek()->startOfWeek();
                $data['datefin'] = Carbon::now()->subWeek()->endOfWeek();
                $data['country'] = $this->countViewPerCountryPerLastWeek();
                $data['browser'] = $this->countViewPerBrowserPerLastWeek();
                $data['device'] = $this->countViewPerDevicePerLastWeek();
                break;
            case 'cmois':
                $data['view'] = $this->MonthViewCount();
                $data['datedeb'] = Carbon::now()->startOfMonth();
                $data['datefin'] = Carbon::now()->endOfMonth();
                $data['country'] = $this->countViewPerCountryPerMonth();
                $data['browser'] = $this->countViewPerBrowserPerMonth();
                $data['device'] = $this->countViewPerDevicePerMonth();

                break;
            case 'dmois':
                $data['view'] = $this->LastMonthViewCount();
                $data['datedeb'] = Carbon::now()->subWeek()->startOfMonth();
                $data['datefin'] = Carbon::now()->subWeek()->endOfMonth();
                $data['country'] = $this->countViewPerCountryPerLastMonth();
                $data['browser'] = $this->countViewPerBrowserPerLastMonth();
                $data['device'] = $this->countViewPerDevicePerLastMonth();
                break;
            default:
                $data['view'] = $this->WeekViewCount();
                $data['datedeb'] = Carbon::now()->startOfWeek();
                $data['datefin'] = Carbon::now()->endOfWeek();
                $data['country'] = $this->countViewPerCountryPerWeek();
                $data['browser'] = $this->countViewPerBrowserPerWeek();
                $data['device'] = $this->countViewPerDevicePerWeek();
                break;
        }
        $range = array();
        if ($date1 != "" && $date2 != "") {
            $data['query'] = "";
            $data['view'] = $this->viewCountBetween2Date(Carbon::parse($date1), Carbon::parse($date2));
            $data['datedeb'] = Carbon::parse($date1)->format('Y-m-d');
            $data['datefin'] = Carbon::parse($date2)->format('Y-m-d');
            $data['country'] = $this->viewCountPerCountryBetween2Date($date1, $date2);
            $data['browser'] = $this->viewCountPerBrowserBetween2Date($date1, $date2);
            $data['device'] = $this->viewCountPerDeviceBetween2Date($date1, $date2);
        } else {
            $data['query'] = $query;
        }
        return $data;
    }

    public function WeekViewCount()
    {
        return $this->counterViewRepository->countViewWeek();
    }

    public function countViewPerCountryPerWeek()
    {
        return $this->counterViewRepository->countViewPerCountryPerWeek();
    }

    public function countViewPerBrowserPerWeek()
    {
        return $this->counterViewRepository->countViewPerBrowserPerWeek();
    }

    public function countViewPerDevicePerWeek()
    {
        return $this->counterViewRepository->countViewPerDevicePerWeek();
    }

    public function LastWeekViewCount()
    {
        return $this->counterViewRepository->countViewLastWeek();
    }

    public function countViewPerCountryPerLastWeek()
    {
        return $this->counterViewRepository->countViewPerCountryPerLastWeek();
    }

    public function countViewPerBrowserPerLastWeek()
    {
        return $this->counterViewRepository->countViewPerBrowserPerLastWeek();
    }

    public function countViewPerDevicePerLastWeek()
    {
        return $this->counterViewRepository->countViewPerDevicePerLastWeek();
    }

    public function MonthViewCount()
    {
        return $this->counterViewRepository->countViewMonth();
    }

    public function countViewPerCountryPerMonth()
    {
        return $this->counterViewRepository->countViewPerCountryPerMonth();
    }

    public function countViewPerBrowserPerMonth()
    {
        return $this->counterViewRepository->countViewPerBrowserPerMonth();
    }

    public function countViewPerDevicePerMonth()
    {
        return $this->counterViewRepository->countViewPerDevicePerMonth();
    }

    public function LastMonthViewCount()
    {
        return $this->counterViewRepository->countViewLastMonth();
    }

    public function countViewPerCountryPerLastMonth()
    {
        return $this->counterViewRepository->countViewPerCountryPerLastMonth();
    }

    public function countViewPerBrowserPerLastMonth()
    {
        return $this->counterViewRepository->countViewPerBrowserPerLastMonth();
    }

    public function countViewPerDevicePerLastMonth()
    {
        return $this->counterViewRepository->countViewPerDevicePerLastMonth();
    }

    public function viewCountBetween2Date($date1, $date2)
    {
        return $this->counterViewRepository->viewCountBetween2Date($date1, $date2);
    }

    public function viewCountPerCountryBetween2Date($date1, $date2)
    {
        return $this->counterViewRepository->viewCountPerCountryBetween2Date($date1, $date2);
    }

    public function viewCountPerBrowserBetween2Date($date1, $date2)
    {
        return $this->counterViewRepository->viewCountPerBrowserBetween2Date($date1, $date2);
    }

    public function viewCountPerDeviceBetween2Date($date1, $date2)
    {
        return $this->counterViewRepository->viewCountPerDeviceBetween2Date($date1, $date2);
    }
}
