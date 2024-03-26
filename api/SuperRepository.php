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
 * Class SuperRepository
 * @package api
 */
abstract class SuperRepository
{
    const PARAMS = [
        'host' => 'host.docker.internal',
        'user' => 'api_demo',
        'password' => 'api_demo123',
        'dbname' => 'api_demo',
    ];

    public $link;

    /** @var array */
    private $params;

    /**
     * SuperRepository constructor.
     */
    public function __construct()
    {
        $this->params = self::PARAMS;
        $this->setLink();
    }

    /**
     * @return array
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        if($this->link->client_info == false){
            $this->setLink();
        }
        return $this->link;
    }

    /**
     * Set Link
     */
    private function setLink(): void
    {
        $params = $this->getParams();
        if(empty($this->link) || $this->link->client_info == false){
            $this->link = mysqli_connect($params['host'], $params['user'], $params['password'], $params['dbname']);
        }
    }

    /**
     * @param string $sql
     * @return array
     */
    protected function getFetchRows($sql = ''): array
    {
        $resultArr = [];
        if ($this->link && $sql != '') {
            $result = mysqli_query($this->link, $sql);
            $rows = mysqli_num_rows($result);
            if (isset($rows) && $rows > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $resultArr[] = $row;
                }
            }
            mysqli_close($this->link);
        }
        return $resultArr;
    }
}
