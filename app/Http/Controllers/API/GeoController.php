<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use GeoIp2\Database\Reader;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 3/2/2017
 * Time: 1:54 PM
 */
class GeoController extends Controller
{
    var $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function countryByIP($ip_address = null)
    {
        if (is_null($ip_address)) {
            $ip_address = $this->request->ip();
        }

        $reader = new Reader(public_path() . '/GeoLite2-Country.mmdb');
        try {
            $record = $reader->country($ip_address);
            $city = $reader->city($ip_address);
            $status = true;
            if (isset($record->country) && isset($record->country->isoCode)) {
                $country_code = $record->country->isoCode;
                if ($this->request->has('callback')) {
                    return response()->json(compact(['status', 'country_code']))->setCallback($this->request->get('callback'));
                } else {
                    return compact(['status', 'country_code']);
                }
            } else {
                $status = false;
                if ($this->request->has('callback')) {
                    return response()->json(compact(['status']))->setCallback($this->request->get('callback'));
                } else {
                    return compact(['status']);
                }
            }
        } catch (AddressNotFoundException $addressNotFoundException) {
            $status = false;
            if ($this->request->has('callback')) {
                return response()->json(compact(['status']))->setCallback($this->request->get('callback'));
            } else {
                return compact(['status']);
            }
        }
    }

    public function stateByIP($ip_address = null)
    {
        if (is_null($ip_address)) {
            $ip_address = $this->request->ip();
        }

        $reader = new Reader(public_path() . '/GeoLite2-City.mmdb');
        try {
            $record = $reader->city($ip_address);
            $status = true;
            if (isset($record->subdivisions) && isset($record->subdivisions->iso_code)) {
                $state_code = $record->subdivisions->iso_code;
                if ($this->request->has('callback')) {
                    return response()->json(compact(['status', 'state_code']))->setCallback($this->request->get('callback'));
                } else {
                    return compact(['status', 'state_code']);
                }
            } else {
                $status = false;
                if ($this->request->has('callback')) {
                    return response()->json(compact(['status']))->setCallback($this->request->get('callback'));
                } else {
                    return compact(['status']);
                }
            }
        } catch (AddressNotFoundException $addressNotFoundException) {
            $status = false;
            if ($this->request->has('callback')) {
                return response()->json(compact(['status']))->setCallback($this->request->get('callback'));
            } else {
                return compact(['status']);
            }
        }
    }

    public function cityByIP($ip_address = null)
    {
        if (is_null($ip_address)) {
            $ip_address = $this->request->ip();
        }

        $reader = new Reader(public_path() . '/GeoLite2-City.mmdb');
        try {
            $record = $reader->city($ip_address);
            $status = true;
            if (isset($record->city) && isset($record->city->names)) {
                $city_name = $record->city->names['en'];
                if ($this->request->has('callback')) {
                    return response()->json(compact(['status', 'city_name']))->setCallback($this->request->get('callback'));
                } else {
                    return compact(['status', 'city_name']);
                }
            } else {
                $status = false;
                if ($this->request->has('callback')) {
                    return response()->json(compact(['status']))->setCallback($this->request->get('callback'));
                } else {
                    return compact(['status']);
                }
            }
        } catch (AddressNotFoundException $addressNotFoundException) {
            $status = false;
            if ($this->request->has('callback')) {
                return response()->json(compact(['status']))->setCallback($this->request->get('callback'));
            } else {
                return compact(['status']);
            }
        }
    }

    public function allByIP($ip_address = null)
    {
        if (is_null($ip_address)) {
            $ip_address = $this->request->ip();
        }

        $reader = new Reader(public_path() . '/GeoLite2-City.mmdb');
        try {
            $record = $reader->city($ip_address);
            $status = true;


            if (isset($record->city) && isset($record->city->names)) {
                $city_name = $record->city->names['en'];
            }
            if (isset($record->subdivisions) && !empty($record->subdivisions)) {

                $state_code = array_first($record->subdivisions)->isoCode;
            }
            if (isset($record->country) && isset($record->country->isoCode)) {
                $country_code = $record->country->isoCode;
            }
            if ($this->request->has('callback')) {
                return response()->json(compact(['status', 'city_name', 'state_code', 'country_code']))->setCallback($this->request->get('callback'));
            } else {
                return compact(['status', 'city_name', 'state_code', 'country_code']);
            }
        } catch (AddressNotFoundException $addressNotFoundException) {
            $status = false;
            if ($this->request->has('callback')) {
                return response()->json(compact(['status']))->setCallback($this->request->get('callback'));
            } else {
                return compact(['status']);
            }
        }
    }
}