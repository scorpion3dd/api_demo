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
 * Class Ticket
 * @package api
 */
class Ticket extends TicketRepository
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $flight_id;

    /**
     * @var int
     */
    private $place;

    /**
     * @var int
     */
    private $suma = 0;

    /**
     * @var string
     */
    private $user_email;

    /**
     * @var int
     */
    private $date_create;

    /**
     * Ticket constructor.
     * @param $params
     */
    function __construct($params = [])
    {
        parent::__construct();
        $this->init($params);
    }

    /**
     * @param $params
     *
     * @return bool
     */
    public function init($params): bool
    {
        if(!empty($params)){
            $this->id = isset($params['id']) ? (int)trim((string)$params['id']) : '';
            $this->flight_id = isset($params['flight_id']) ? $params['flight_id'] : '';
            $this->place = isset($params['place']) ? $params['place'] : '';
            $this->suma = isset($params['suma']) ? $params['suma'] : '';
            $this->user_email = isset($params['user_email']) ? trim($params['user_email']) : '';
            $this->date_create = time();
            return true;
        }
        return false;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getFlightId(): ?int
    {
        return $this->flight_id;
    }

    /**
     * @param int $flight_id
     */
    public function setFlightId(int $flight_id): void
    {
        $this->flight_id = $flight_id;
    }

    /**
     * @return int
     */
    public function getPlace(): ?int
    {
        return $this->place;
    }

    /**
     * @param int $place
     */
    public function setPlace(int $place): void
    {
        $this->place = $place;
    }

    /**
     * @return string
     */
    public function getUserEmail(): ?string
    {
        return $this->user_email;
    }

    /**
     * @param string $user_email
     */
    public function setUserEmail(string $user_email): void
    {
        $this->user_email = $user_email;
    }

    /**
     * @return int
     */
    public function getSuma(): ?int
    {
        return $this->suma;
    }

    /**
     * @param int $suma
     */
    public function setSuma(int $suma): void
    {
        $this->suma = $suma;
    }

    public function getDateCreate()
    {
        return $this->date_create;
    }

    /**
     * @param $date_create
     */
    public function setDateCreate($date_create): void
    {
        $this->date_create = $date_create;
    }
}
