using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace TripletChallenge
{
    class Program
    {
        static void Main(string[] args)
        {
            // Check input
            if (args.Length != 1)
            {
                Console.WriteLine("Unknown argument format.");
                Console.WriteLine("Usage: dotnet run -c Release --project ./src/ ./pg2009.txt");
                return;
            }

            string filePath = args[0];
            if (!File.Exists(filePath))
            {
                Console.WriteLine("File does not exist.");
                return;
            }

            // Find all triplets and put them into a dictionary, together with their number of ocurrences
            Dictionary<string, int> tripletDictionary = new Dictionary<string, int>();
            using (StreamReader fileReader = new StreamReader(filePath))
            {
                foreach (string triplet in TripletEnumerator(fileReader))
                {
                    if (tripletDictionary.ContainsKey(triplet))
                    {
                        tripletDictionary[triplet]++;
                    }
                    else
                    {
                        tripletDictionary[triplet] = 1;
                    }
                }
            }

            // Sort by descending order and write the first 3 results to the console
            var orderedTriplets = tripletDictionary.OrderByDescending(x => x.Value).Take(3);
            foreach (var triplet in orderedTriplets)
            {
                Console.WriteLine($"{triplet.Key} - {triplet.Value}");
            }
        }

        private static IEnumerable<string> TripletEnumerator(StreamReader fileReader)
        {
            Queue<string> tripletQueue = new Queue<string>(3);
            foreach (var word in WordEnumerator(fileReader))
            {
                tripletQueue.Enqueue(word);
                if (tripletQueue.Count == 3)
                {
                    string[] tripletArray = tripletQueue.ToArray();
                    yield return tripletArray[0] + " " + tripletArray[1] + " " + tripletArray[2];
                    tripletQueue.Dequeue();
                }
            }
        }

        private static IEnumerable<string> WordEnumerator(StreamReader fileReader)
        {
            StringBuilder wordBuilder = new StringBuilder();
            while (!fileReader.EndOfStream)
            {
                char nextChar = (char)fileReader.Read();
                if (IsValidEnglishChar(nextChar))
                {
                    wordBuilder.Append(nextChar);
                    continue;
                }
                if (wordBuilder.Length != 0)
                {
                    yield return wordBuilder.ToString().ToLower();
                    wordBuilder.Clear();
                }
            }
            if (wordBuilder.Length != 0) { yield return wordBuilder.ToString().ToLower(); }
        }

        private static bool IsValidEnglishChar(char inputChar)
        {
            if (Char.IsLetterOrDigit(inputChar)) { return true; }
            if (inputChar == '\'') { return true; }
            return false;
        }
    }
}