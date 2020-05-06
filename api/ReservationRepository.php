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
 * Class ReservationRepository
 * @package api
 */
class ReservationRepository extends SuperRepository implements Repository
{
    /**
     * ReservationRepository constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $items = $this->getFetchRows("SELECT * FROM reservation");
        if(isset($items)){
            return $items;
        }
        return [];
    }

    /**
     * @param int $id
     * @return \stdClass|null
     */
    public function getById($id = 0)
    {
        $items = $this->getFetchRows("SELECT * FROM reservation WHERE id = $id");
        if(isset($items[0])){
            return $items[0];
        }
        return null;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getByFlight($id = 0): array
    {
        $items = $this->getFetchRows("SELECT * FROM reservation WHERE flight_id = $id");
        if(!empty($items)){
            return $items;
        }
        return [];
    }

    /**
     * @param int $flight_id
     * @param int $place
     * @return |null
     */
    public function getByFlightPlace($flight_id = 0, $place = 0)
    {
        $items = $this->getFetchRows("SELECT * FROM reservation 
            WHERE flight_id = $flight_id  AND  place = $place");
        if(isset($items[0])){
            return $items[0];
        }
        return null;
    }

    /**
     * @return bool
     */
    public function update(): bool
    {
        $reservation = $this->getById($this->getId());
        if(!empty($reservation)){
            $sql = "UPDATE reservation  SET 
                    flight_id = ?,
                    place = ?,
                    user_email = ?,
                    date_create = Now()
                WHERE id = ?";
            $stmt = mysqli_prepare($this->getLink(), $sql);
            if(!empty($this->getFlightId()) && !empty($this->getPlace())
                && !empty($this->getUserEmail()) && !empty($this->getId()))
            {
                mysqli_stmt_bind_param($stmt, "ssss", $this->getFlightId(), $this->getPlace(),
                    $this->getUserEmail(), $this->getId());
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                return true;
            }
        }
        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteById($id = 1): bool
    {
        $sql = "DELETE  FROM  reservation  WHERE  id = ?";
        $stmt = mysqli_prepare($this->getLink(), $sql);
        if(!empty($id))
        {
            mysqli_stmt_bind_param($stmt, "s", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return true;
        }
        return false;
    }

    /**
     * @return int
     */
    public function saveNew(): int
    {
        $reservation = $this->getByFlightPlace($this->getFlightId(), $this->getPlace());
        if(empty($reservation)){
            $sql = "INSERT INTO reservation (flight_id, place, user_email, date_create) 
                VALUES (?, ?, ?, Now())";
            $stmt = mysqli_prepare($this->getLink(), $sql);
            if(!empty($this->getFlightId()) && !empty($this->getPlace()) && !empty($this->getUserEmail()))
            {
                mysqli_stmt_bind_param($stmt, "sss",
                    $this->getFlightId(), $this->getPlace(), $this->getUserEmail());
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $id = mysqli_insert_id($this->getLink());
                if(!empty($id)){
                    return $id;
                }
            }
        }
        return 0;
    }
}
