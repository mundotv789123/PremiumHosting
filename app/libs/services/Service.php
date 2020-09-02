<?php

namespace app\libs\services;

use app\libs\customers\Authentication;
use app\libs\customers\Profile;
use app\libs\customers\Wallet;
use app\libs\security\Ajax;
use app\libs\utils\JsonPretty;
use app\libs\utils\Mail;
use app\system\DB;

class Service
{

    private $db;

    public function __construct()
    {
        $this->db = new DB();
        $this->__table();
    }

    public function store()
    {
        if(!Ajax::check()) {
            return JsonPretty::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }
        if(empty($_POST['planId'])) {
            return JsonPretty::encode([ 'success' => false, 'message' => 'Informe todos os dados' ]);
        }

        $plan = new Plan();

        $price = $plan->data($_POST['planId'])->price;
        $wallet = Profile::wallet();

        if($price > $wallet) {
            return JsonPretty::encode([ 'success' => false, 'message' => 'Você não tem saldo suficiente, faça uma recarga.' ]);
        }

        $customerId = Authentication::id();

        $insert = $this->db->insert([
            'plan_id' => $_POST['planId'],
            'customer_id' => $customerId,
            'status' => ServiceEnum::PENDING,
            'expire_at' => date('Y-m-d', strtotime(date("Y-m-d") . '+ 30 days')),
            'created_at' => date('Y-m-d')
        ], 'services');

        $wallet = new Wallet();
        $wallet->remove($price, Authentication::id());

        $email = Profile::email($customerId);

        $html = file_get_contents('./app/templates/emails/service_pending.html');
        Mail::send($email, "Serviço {$insert['id']} pago!", str_replace('{id}', $insert['id'], $html));

        return JsonPretty::encode([ 'success' => true, 'message' => 'Seu serviço foi contratado e está pendente' ]);
    }

    public function active()
    {
        if(!Ajax::check()) {
            return JsonPretty::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }

        if(empty($_POST['id']) || empty($_POST['names']) || empty($_POST['values'])) {
            return JsonPretty::encode([ 'success' => false, 'message' => 'Informe todos os dados' ]);
        }

        $i = -1;
        $data = [];

        foreach ($_POST['names'] as $name) {
            $i++;

            $value = $_POST['values'][$i];
            $data[] = [
                'name' => $name,
                'value' => $value
            ];
        }

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $select = $this->db->select("SELECT * FROM `services` WHERE `id`=?", [$_POST['id']])[0];

        if($select->data == null) {
            $serviceId = 10000 + $_POST['id'];
            $email = Profile::email($select->customer_id);

            $html = file_get_contents('./app/templates/emails/service_active.html');
            $dataValues = '';

            foreach (json_decode($data) as $item) {
                $dataValues .= '<p><b>'.$item->name.':</b> '.$item->value.'</p>';
            }

            Mail::send($email, "Serviço {$serviceId} ativo!", str_replace('{data}', $dataValues, $html));
        }

        $this->db->update([
            'data' => $data,
            'status' => ServiceEnum::ACTIVE
        ], [ 'id' => $_POST['id'] ], 'services');

        return JsonPretty::encode([ 'success' => true, 'message' => 'Serviço ativo' ]);
    }

    public function delete($id)
    {
        return $this->db->update([
            'status' => ServiceEnum::REMOVED
        ], ['id' => $id], 'services');
    }

    public function renew()
    {
        if(!Ajax::check()) {
            return JsonPretty::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }

        $id = $_POST['id'];

        $data = $this->db->select("SELECT * FROM `services` WHERE `id`=?", [$id])[0];
        $planId = $data->plan_id;

        $plan = new Plan();

        $price = $plan->data($planId)->price;
        $wallet = Profile::wallet();

        if($price > $wallet) {
            return JsonPretty::encode([ 'success' => false, 'message' => 'Você não possui saldo suficiente! Recarregue' ]);
        }

        $expireAt = $data->expire_at;
        $today = date('Y-m-d');

        if(strtotime($today) <= strtotime($expireAt)) {
            $newDate = date('Y-m-d', strtotime($expireAt . ' + 30 days'));
        }else{
            $newDate = date('Y-m-d', strtotime($today . ' + 30 days'));
        }

        $this->db->update([
            'expire_at' => $newDate,
            'status' => 'PENDING'
        ], ['id' => $id], 'services');

        $wallet = new Wallet();
        $wallet->remove($price, Authentication::id());

        return JsonPretty::encode([ 'success' => true, 'message' => 'Serviço reativado e enviado para liberação' ]);
    }

    public function pendings($id = null)
    {
        if($id) {
            return $this->db->select("SELECT * FROM `services` WHERE `status`=? AND `id`=?", ['PENDING', $id])[0];
        }

        return $this->db->select("SELECT * FROM `services` WHERE `status`=? ORDER BY `id` DESC", ['PENDING']);
    }

    public function has($id)
    {
        return $this->db->select("SELECT * FROM `services` WHERE `id`=? AND `customer_id`=?", [$id, Authentication::id()]);
    }

    public function cron() {
        $today = date('Y-m-d');
        $previewDate = date('Y-m-d', strtotime($today . ' + 2 days'));

        $dataAlert = $this->db->select("SELECT * FROM `services` WHERE `expire_at`=?", [$previewDate]);
        $alertCount = 0;

        foreach ($dataAlert as $item) {
            $email = Profile::email($item->customer_id);

            $html = file_get_contents('./app/templates/emails/register.html');
            $serviceId = 10000 + $item->id;

            Mail::send($email, 'Aviso de suspensão', str_replace('{id}', $serviceId, $html));

            $alertCount++;
        }

        $dataSuspense = $this->db->select("SELECT * FROM `services` WHERE `expire_at`=?", [$today]);

        $wallet = new Wallet();
        $plan = new Plan();

        $suspenseCount = 0;
        $renewCount = 0;

        foreach ($dataSuspense as $item) {
            $email = Profile::email($item->customer_id);

            $wallet = $wallet->get($item->customer_id);
            $price = $plan->data($item->id)->price;

            if($price > $wallet) {
                $html = file_get_contents('./app/templates/emails/service_pending.html');
                $serviceId = 10000 + $item->id;

                Mail::send($email, 'Serviço '.$serviceId.' suspenso!', str_replace('{id}', $serviceId, $html));

                $suspenseCount++;
            }else{
                $expireAt = $item->expire_at;
                $newDate = date('Y-m-d', strtotime($expireAt . ' + 30 days'));

                $this->db->update([
                    'expire_at' => $newDate
                ], [ 'id' => $item->id ], 'services');

                $renewCount++;
            }
        }

        echo  "<b>Alertas enviados: </b> {$alertCount}<br><b>Serviços suspensos: </b> {$suspenseCount} <br> <b>Renovações efetuadas:</b> {$renewCount}";
    }

    public function data($id = null)
    {
        if($id) {
            return $this->db->select("SELECT * FROM `services` WHERE `id`=?", [$id])[0];
        }

        return $this->db->select("SELECT * FROM `services` ORDER BY `id` DESC");
    }

    private function __table()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `services` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `plan_id` INT(11) NOT NULL , `customer_id` INT(11) NOT NULL , `data` JSON NULL DEFAULT NULL , `status` ENUM('PENDING','ACTIVE','SUSPENSE','REMOVED') NOT NULL , `expire_at` DATE NOT NULL , `created_at` DATE NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;");
    }

}