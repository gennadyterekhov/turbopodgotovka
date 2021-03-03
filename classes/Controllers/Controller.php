<?php



class Controller
{
    public $entity;


    public function __construct($entity)
    {
        $this->entity = $entity;
    }


    // создать строку в таблице
    public function create($request)
    {
        $fields = $this->entity->fields;
        $params = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $request) == false) {
                return ['code' => 500, 'message' => "$field not found in request"];
            }
            // $params[$fieldName] = $fieldValue;
            $params[] = $request[$field];
        }

        // return call_user_func_array([$this->entity, 'create'], $params)->execute();
        call_user_func_array([$this->entity, 'create'], $params);
        return ['code' => 200];
    }


    public function get($request)
    {
        if (array_key_exists('id', $request)) {
            return $this->entity->get($request['id'])->fetchAll(PDO::FETCH_ASSOC);
        }
        return ['code' => 500, 'message' => 'id not found'];
    }


    public function select($request)
    {
        return $this->entity->select()->fetchAll(PDO::FETCH_ASSOC);
    }


    // обновить строку в таблице
    public function update($request)
    {
        $fields = $this->entity->fields;
        array_unshift($fields, 'id');
        $params = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $request) == false) {
                return ['code' => 500, 'message' => "$field not found in request"];
            }
            // $params[$fieldName] = $fieldValue;
            $params[] = $request[$field];
        }

        call_user_func_array([$this->entity, 'update'], $params);
        return ['code' => 200];
    }


    public function deleteAll($request)
    {
        return $this->entity->deleteAll()->execute();
    }


    public function delete($request)
    {
        if (array_key_exists('id', $request)) {
            return $this->entity->delete($request['id'])->execute();
        }
        return ['code' => 500, 'message' => 'id not found'];
    }
}
