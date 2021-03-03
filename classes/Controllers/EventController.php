<?php

require_once dirname(__FILE__) . '/Controller.php';

class EventController extends Controller
{

    // // создать строку в таблице
    // public function create($data)
    // {

    //     if (array_key_exists('name', $data)) {
    //         $name = $data['name'];
    //         return $this->entity->create($name)->execute();
    //     }
    //     return ['code' => 500, 'message' => 'id not found'];

    // }


    // обновить строку в таблице
    // public function update($data)
    // {

    //     if (array_key_exists('name', $data) && array_key_exists('id', $data)) {
    //         $name = $data['name'];
    //         $id = $data['id'];
    //         return $this->entity->update($id, $name)->execute();
    //     }
    //     return ['code' => 500, 'message' => 'id or name not found'];
    // }



}
