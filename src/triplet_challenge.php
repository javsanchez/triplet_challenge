<?php

/**
 * triplet_challenge is an application that extracts the top 3 triplet words of a file
 *
 * Copyright (C) 2018 Javier Sánchez Fandiño
 *
 * This file is part of triplet_challenge.
 *
 * triplet_challenge is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * triplet_challenge is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with triplet_challenge.  If not, see <http://www.gnu.org/licenses/>.
 */

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
