<?php

public function add_external_duty(){  //PERSONELİN DIŞ GÖREV İZNİ OLUŞTURMASI İÇİN HAZIRLANAN CONTROLLER

    $method_name    =   "dis_gorev_listem"; 
    $access         =   access_control($this->controller,$method_name,loginstaff("PERSONNEL_ID"));  //KULLANICI GİRİŞ YAPMIŞ MI VE BU SAYFAYI GÖRMEYE İZNİ VAR MI KONTROLÜ

    if($access == 0)
    { //GÖRÜNTÜLEME İZNİ YOKSA invalid_access EKRANI GÖSTERİLİR
          $this->invalid_access();
    }   
    else    
    {
        $personnel_id   = $this->input->post('personnel_id');
        $duty_place     = $this->input->post('duty_place');
        $project_id     = $this->input->post('project_id');
        $reason_for_go  = $this->input->post('reason_for_go');
        $check_out_time = $this->input->post('check_out_time');
        $duty_planning  = $this->input->post('duty_planning');
        $executive_id   = $this->input->post('executive_id');
        $planning_time  = $this->input->post('planning_time');
        $car_state      = $this->input->post('car_state');

        $data           =   array(  //VERITABANINA INSERT EDILMESI ISTENILEN ARRAYI DEĞERLERİNE EŞİTLİYORUZ
            "PERSONNEL_ID"             =>   $personnel_id,
            "DUTY_PLACE"               =>   $duty_place,
            "PROJECT_ID"               =>   $project_id,
            "REASON_FOR_GO"            =>   $reason_for_go,
            "EXECUTIVE_ID"             =>   $executive_id,
            "CHECK_OUT_TIME"           =>   $check_out_time,
            "EXECUTIVE_ADMINISTRATION" =>   0,
            "DUTY_PLANNING"            =>   $duty_planning,
            "DUTY_STATUS"              =>   0,
            "PLANNING_RETURN_TIME"     =>   $planning_time,
            "CAR_STATE"                =>   $car_state=="on" ? 1 : 0,
        );

        $insert         =   $this->ExternalDuty_model->insert($data);  //VERITABANINA INSERT İŞLEMİ GERÇEKLEŞTİRİLİYOR

        if($insert)
        {  //INSERT İŞLEMİ BAŞARILI OLURSA
            if($CAR_STATE=="on")
            {  //VE PERSONELE VERİLECEK OLAN ARAÇ O AN MÜSAİTSE
                $car_data   =   array(
                    "personnel_id"         =>   $personnel_id,
                    "auth_id"              =>   $executive_id,
                    "description"          =>   $reason_for_go,
                    "check_out_date"       =>   $check_out_time,
                    "planning_return_time" =>   $planning_time,
                    "createdat"            =>   date("Y-m-d")
                );

                $this->db->insert("CAR_MOVEMENTS",$car_data);  //DIŞ GÖREV İZNİ OLUŞTURULUR
            }

            $process        =   "Dış görev izni ekledi";  //SİSTEMDE YAPILAN HER İŞLEMİ LOGLAMAK İÇİN VERİTABANINA KAYDEDİLİYOR
            $this->add_to_log($process); //LOG VERİTABANINA KAYDEDİLİYOR
            $notification   =   "Yeni dış görev talebi gerçekleştirildi."; //PERSONELE İŞLEM BAŞARILI BİLDİRİMİ HAZIRLANIYOR
            add_notification($notification,$executive_id);  //PERSONELE İŞLEM BAŞARILI BİLDİRİMİ GÖNDERİLİYOR

            $alert = array( //BAŞARILI OLURSA KULLANICIYA GÖSTERİLECEK TOAST ALERT İÇERİĞİ HAZIRLANIYOR
                "title" => "İşlem Başarılıdır",
                "text"  => "Dış Görev Formunuz Başarılı Bir Şekilde Tanımlandı.",
                "type"  => "success"
            );
        }
        else
        { //HERHANGİ BİR ADIMDA BAŞARISIZ OLMA DURUMUNDA GÖNDERİLECEK TOAST MESAJI HAZIRLANIYOR
            $alert = array(
                "title" => "İşlem Başarısızdır",
                "text" => "Dış Görev Formunuz Oluşturulamadı !",
                "type" => "error"
            );
        }

        $this->session->set_flashdata('alert', $alert);
        redirect(base_url("Formlar/dis_gorev_listem")); //YENİ İZİN EKLE SAYFASINA YÖNLENDİRME YAPILIYOR
    }
}