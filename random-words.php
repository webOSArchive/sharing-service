<?php
$rand_text = trim(get_random_word("adjectives"));
$rand_text = $rand_text . " " . trim(get_random_word("nouns"));

function get_random_word($word_type) {
    $file = "dict/" . $word_type . ".txt";
    $file_arr = file($file);
    $num_lines = count($file_arr);
    $last_arr_index = $num_lines - 1;
    $rand_index = rand(0, $last_arr_index);
    return $file_arr[$rand_index];
}

echo $rand_text;
?>