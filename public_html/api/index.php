<?php
require 'config.php';

function load_data_from_api($sensebox_id) {
  // https://api.opensensemap.org/boxes/59be7e67d67eb50011d72f40
  $data = file_get_contents('https://api.opensensemap.org/boxes/' . $sensebox_id);
  $json = json_decode($data, true);
  // var_dump($json);
  return $json;
}

function create_slimmer_json($data) {
  $slim_data = array(
    'time' => time(),
    'sensors' => array()
  );

  if($config['include_name']) {
    $slim_data['name'] = $data['name'];
  }
  
  foreach ($data['sensors'] as $sensor) {
    $slim_sensor = array(
      'title' => $sensor['title'],
      'unit' => $sensor['unit'],
      'sensortype' => $sensor['sensorType'],
      'value' => $sensor['lastMeasurement']['value'],
      'time' => $sensor['lastMeasurement']['createdAt']
    );
    $slim_data[] = $slim_sensor;
  }
  return $slim_data;
}

function save_data_in_cache($name, $data) {
  // chmod 777 cache/
  file_put_contents('cache/' . $name . '.json', json_encode($data));
}

function update($name) {
  global $config;           // use global config

  if (!array_key_exists($name, $config['senseboxes'])) {  // not existing
    return array('error' => 'Does not exist');            // return error
  }
  $sensebox_id = $config['senseboxes'][$name];            // get corresponding id for API
  $data = load_data_from_api($sensebox_id);               // make API-call
  $json = create_slimmer_json($data);                     // create slim version of API-response
  save_data_in_cache($name, $json);                       // save json to cache

  return $json;
}

function read_data_from_cache($name) {
  $data = file_get_contents('cache/' . $name . '.json');
  return json_decode($data, true);
}

$requested_name = $_GET['q'];
$response_json;

if (!file_exists('cache/' . $requested_name . '.json')) { // cache-file does not exist
  $response_json = update($requested_name);               // create it
} else {
  $response_json = read_data_from_cache($requested_name);

  $dif = time() - $response_json['time'];                 // time since update

  if ($dif > ($config['interval'] * 60)) {
    $response_json = update($requested_name);             // update data (and store it)
  }
}

header('Content-type: application/json');   // set header
echo json_encode($response_json);           // send
