import sys
import os.path
import io
from collections import deque


def triplet_enumerator(file_reader):
    queue = deque()
    for word in word_enumerator(file_reader):
        queue.append(word)
        if len(queue) == 3:
            yield queue[0] + " " + queue[1] + " " + queue[2]
            queue.popleft()


def word_enumerator(file_reader):
    word = []
    while True:
        char = file_reader.read(1)
        if not char: break
        if is_valid_English_char(char):
            word.append(char)
            continue
        if word:
            yield ''.join(word).lower()
            word.clear()
    if word:
        yield ''.join(word).lower()

def is_valid_English_char(char):
    if str.isalpha(char): return True
    if str.isdigit(char): return True
    if char == '\'': return True
    return False


# Check input
if len(sys.argv) != 2:
    print("Unknown argument format.")
    sys.exit(1)

filePath = sys.argv[1]
if not os.path.isfile(filePath):
    print("File does not exist.")
    sys.exit(1)

# Find all triplets and put them into a dictionary, together with their number of ocurrences
triplet_dictionary = {}
with io.open(filePath, mode="r", encoding="utf-8") as file_reader:
    for triplet in triplet_enumerator(file_reader):
        if triplet in triplet_dictionary:
            triplet_dictionary[triplet] += 1
        else:
            triplet_dictionary[triplet] = 1

# Sort by descending order and write the first 3 results to the console
sorted_triplets = sorted(triplet_dictionary, key=triplet_dictionary.get, reverse=True)
first_three_sorted_triplets = (triplet for _, triplet in zip(range(3), sorted_triplets))

for triplet in first_three_sorted_triplets:
  print(f"{triplet} - {triplet_dictionary[triplet]}")


