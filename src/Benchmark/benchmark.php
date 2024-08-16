<?php

function generate_random_ids($minLength, $maxLength, $minId, $maxId) {
  $length = rand($minLength, $maxLength);
  $ids = [];
  
  for ($i = 0; $i < $length; $i++) {
      $ids[] = rand($minId, $maxId);
  }
  
  return $ids;
}

$minLength = 50;
$maxLength = 200;
$minId = 1;
$maxId = 1000;
$numArrays = 100000;

$randomArrays = [];

$scores = [];

for ($i = 0; $i < $numArrays; $i++) {
  $randomArrays[] = generate_random_ids($minLength, $maxLength, $minId, $maxId);
}

$start = time();
// Output the generated arrays for verification
foreach ($randomArrays as $index => $array) {
  //echo "Array " . ($index + 1) . " (Length: " . count($array) . "):\n";
  //echo implode(", ", $array) . "\n\n";
  $score[$index] = calculate_similarity_percentage(generate_random_ids($minLength, $maxLength, $minId, $maxId), $randomArrays[$index]);
}

arsort($score, SORT_NUMERIC);

$end = time();

var_dump($start);
var_dump($end);
var_dump($end - $start);

var_dump($score);