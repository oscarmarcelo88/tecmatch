<?php

$postbody = '{"setting_type":"call_to_actions","thread_state":"existing_thread","call_to_actions":[{"type":"postback","title":"Cambiar canal","payload":"canal"},{"type":"postback","title":"Rehacer el cuestionario","payload":"borrar"},{"type":"postback","title":"Ver Contactos","payload":"contactos"},{"type":"postback","title":"Cambiar orientación sexual","payload":"cambiarsex"}]}';


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.6/me/thread_settings?access_token=EAAIUReNE8dkBAFikkZBPUzx6NxdLWIJJmtIHCdIT6g37FnvEJLHFNo6YKQNlNFkyr9G1z1qg6B74yNNnYzZAwp4mIO6VpJMsjd6e3Ogd1f4J5X6I2TJuM7d29r1WVCDHkhZAGhyy3kspMfZBLTRUk8ZCepCCCjgvw8w4KSoUaQQZDZD');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postbody);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
$output = curl_exec($ch);
curl_close($ch);

error_log($output);