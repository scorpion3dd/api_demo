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

use \api\SuperApi;

/**
 * Class RestFullAPI
 *
 * @package patterns
 */
class RestFullAPI extends SuperApi
{
    /**
     *
     * @return string
     */
    public function indexAction()
    {
        $obj = $this->getObj();
        if(isset($obj)){
            if($items = $obj->getAll()){
                return $this->response(['items' => $items, 'message' => 'all'], 200);
            }
        }
        return $this->response(['message' => 'Error'], 500);
    }

    /**
     *
     * @return string
     */
    public function viewAction()
    {
        $id = $this->getId();
        if(!empty($id)){
            $obj = $this->getObj();
            if(isset($obj)){
                $data = $obj->getById($id);
                if(!empty($data)){
                    return $this->response(['item' => $data, 'message' => 'viewed'], 200);
                }
            }
        }
        return $this->response(['message' => 'Data not found'], 404);
    }

    /**
     * @return Reservation|Ticket|null
     */
    private function getObj()
    {
        switch ($this->requestUri[2]) {
            case self::API_RESERVATION :
                $obj = new Reservation($this->requestParams);
                break;
            case self::API_TICKET :
                $obj = new Ticket($this->requestParams);
                break;
            case self::API_CALLBACK :
                $obj = new Callback($this->requestParams);
                break;
            default:
                return null;
        }
        return $obj;
    }

    /**
     *
     * @return string
     */
    public function createAction()
    {
        $obj = $this->getObj();
        if(isset($obj)){
            if($obj instanceof Callback){
                if($obj->do()){
                    return $this->response(['message' => 'Does'], 200);
                }
            }
            else{
                if($id = $obj->saveNew()){
                    return $this->response(['id' => $id, 'message' => 'Saved'], 200);
                }
                else return $this->response(['message' => 'Saving Error'], 500);
            }
        }
        return $this->response(['message' => 'Error'], 500);
    }

    /**
     *
     * @return string
     */
    public function updateAction()
    {
        $id = $this->getId();
        if(!empty($id)){
            $obj = $this->getObj();
            if(isset($obj)){
                if($obj->update()){
                    return $this->response(['id' => $id, 'message' => 'Updated'], 200);
                }
            }
        }
        return $this->response(['message' => 'Update error'], 500);
    }

    /**
     *
     * @return string
     */
    public function deleteAction()
    {
        $id = $this->getId();
        if(!empty($id)){
            $obj = $this->getObj();
            if(isset($obj)){
                if($obj->init($obj->getById($id))){
                    if($obj->deleteById($id)){
                        return $this->response(['id' => $id, 'message' => 'Deleted'], 200);
                    }
                }
            }
        }
        return $this->response(['message' => 'Delete error'], 500);
    }
}
