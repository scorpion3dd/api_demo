<?php
/*
 * This file is part of the Simple REST Full API project.
 *
 * (c) Denis Puzik <scorpion3dd@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace api;

/**
 * Class Reservation
 * @package api
 */
class Callback extends ReservationRepository
{
    /** @var int */
    private $flight_id;

    /** @var int */
    private $triggered_at;

    /** @var string */
    private $event;

    /** @var string */
    private $secret_key;

    /**
     * Reservation constructor.
     * @param $params
     */
    function __construct($params = [])
    {
        parent::__construct();
        if(isset($params) && count($params) > 0){
            $this->init($params);
        }
    }

    /**
     * @param $params
     *
     * @return bool
     */
    public function init($params): bool
    {
        if(!empty($params)){
            $this->flight_id = isset($params['flight_id']) ? $params['flight_id'] : '';
            $this->triggered_at = isset($params['triggered_at']) ? $params['triggered_at'] : '';
            $this->event = isset($params['event']) ? $params['event'] : '';
            $this->secret_key = isset($params['secret_key']) ? $params['secret_key'] : '';
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function do(): bool
    {
        if($this->event == 'flight_ticket_sales_completed'){
            // TODO flight_ticket_sales_completed
            return true;
        }
        if($this->event == 'flight_canceled'){
            $obj = new Reservation();
            if(isset($obj)){
                if($items = $obj->getByFlight($this->flight_id)){
                    foreach ($items as $item)
                    {
                        $this->sendEmail($item);
                    }
                }
            }
            $obj = new Ticket();
            if(isset($obj)){
                if($items = $obj->getByFlight($this->flight_id)){
                    foreach ($items as $item)
                    {
                        $this->sendEmail($item);
                    }
                }
            }

            return true;
        }
        return false;
    }

    /**
     * @param array $item
     *
     * @return bool
     */
    public function sendEmail(array $item): bool
    {
        if(!empty($item['flight_id']) && !empty($item['user_email'])){
            $message = "Flight {$item['flight_id']} canceled";
            mail($item['user_email'], 'Flight canceled', $message);
            return true;
        }
        return false;
    }
}
