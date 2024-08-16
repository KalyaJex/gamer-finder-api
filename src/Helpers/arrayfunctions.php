<?php

function calculate_similarity_percentage($list1, $list2) {
  // Convert both lists to arrays if they are not already
  $list1 = (array)$list1;
  $list2 = (array)$list2;

  // Find the intersection of the two lists
  $intersection = array_intersect($list1, $list2);

  // Find the union of the two lists (all unique elements)
  $union = array_unique(array_merge($list1, $list2));

  // Calculate the similarity percentage
  $similarity_percentage = round((count($intersection) / count($union)) * 100, 3);

  return $similarity_percentage;
}

function jaccardIndex(array $set1, array $set2) {
  $union = count(array_unique(array_merge($set1, $set2)));
  if ($union === 0) return 0;
  $intersection = count(array_intersect($set1, $set2));
  return $intersection / $union;
}

function arrayToObject(array $data, string $className) {
  //var_dump($data);
  if (!class_exists($className)) {
    throw new InvalidArgumentException("Class $className does not exist.");
  }
  //var_dump(new $className());

  $object = new $className();

  foreach ($data as $key => $value) {
    if (property_exists($object, $key)) {
      $object->$key = $value;
    }
  }

  return $object;
}

function objectToArray($object) {
  if (!is_object($object) && !is_array($object)) {
    return $object;
  }

  $array = [];

  foreach ($object as $key => $value) {
    $array[$key] = objectToArray($value);
  }

  return $array;
}