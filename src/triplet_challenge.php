<?php

// Check input
if (count($argv) != 2)
{
    echo "Unknown argument format.".PHP_EOL;
    exit(1);
}

$filePath = $argv[1];
if (!file_exists($filePath))
{
    echo "File does not exist.".PHP_EOL;
    exit(1);
}

// Find all triplets and put them into a dictionary, together with their number of ocurrences
$triplet_dictionary = [];
$handle = fopen($filePath, "r");
foreach(triplet_enumerator($handle) as $triplet)
{
    if (array_key_exists($triplet, $triplet_dictionary))
    {
        $triplet_dictionary[$triplet] += 1;
    }
    else
    {
        $triplet_dictionary[$triplet] = 1;
    }
}
fclose($handle);

// Sort by descending order and write the first 3 results to the console
arsort($triplet_dictionary);
$first_three_sorted_triplets = array_slice($triplet_dictionary, 0, 3, True);
foreach($first_three_sorted_triplets as $key => $value)
{
    echo $key." - ".$value.PHP_EOL;
}

// Enumerators
function triplet_enumerator($handle)
{
    $queue = [];
    foreach(word_enumerator($handle) as $word)
    {
        $queue[] = $word;
        if (count($queue) == 3)
        {
            yield $queue[0]." ".$queue[1]." ".$queue[2];
            array_shift($queue);
        }
    }
}

function word_enumerator($handle)
{
    $word = [];
    while (True)
    {
        $char = fread($handle, 1);
	if ($char === False || feof($handle)){ break; }
        if (is_valid_English_char($char))
        {
            $word[] = $char;
            continue;
        }
        if (!empty($word))
        {
            yield strtolower(implode($word));
            $word = [];
        }
    }
        
    if (!empty($word))
    {
        yield strtolower(implode($word));
    }
}

function is_valid_English_char($char)
{
    if (ctype_alnum($char)) {return True;}
    if ($char == "\'") {return True;}
    return False;
}
