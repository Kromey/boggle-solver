<?php
/**
 * Boggle Solver
 * by Travis Veazey
 *
 * This program reads in a boggle board layout and prints out
 * all possible words using the standard rules for Boggle.
 */

/**
 * Dictionary 
 * This class manages the dictionary. It handles reading the file from
 * disk as well as searching through the list to find requested words.
 * 
 * @author Travis Veazey
 */
class Dictionary
{
	/**
	 * wordlist 
	 * This is an array of words pulled from the text file. Numerically-indexed
	 * by the first letter of the word and then in alphabetical order.
	 * 
	 * @var array
	 * @access private
	 */
	private $wordlist;
	private $wordcount;

	/**
	 * __construct 
	 * The class constructor; private to enforce a singleton pattern
	 * 
	 * @access private
	 * @return void
	 */
	private function __construct()
	{
		$file = dirname(__FILE__).'/enable2k.txt';
		$this->processFile($file);
	}

	/**
	 * instance 
	 * This static method is responsible for managing the singleton class
	 * 
	 * @static
	 * @access public
	 * @return Dictionary An instance of the Dictionary class
	 */
	public static function instance()
	{
		static $instance = null;

		if(is_null($instance))
		{
			$instance = new Dictionary;
		}

		return $instance;
	}

	/**
	 * findWord 
	 * Uses a binary search algorithm to find the given word in the list
	 * This is considerably faster than PHP's built-in in_array() or array_search() functions
	 * If the second parameter is set to true, then the search looks for the given word as
	 * a substring at the beginning of another word
	 * 
	 * @param string $word The word to look for
	 * @param bool $partialMatch Whether to perform a partial match
	 * @access public
	 * @return bool True if the word is found, false otherwise
	 */
	public function findWord($word, $partialMatch = false)
	{
		//Set up our index and initial values
		$min = 0;
		$max = $this->wordcount;

		//Run our binary search
		while($min < $max - 1)
		{
			//Compute our offset to find the middle
			$offset = floor(($max - $min) / 2);
			if($offset == 0)
			{
				//Offset of 0 means we've exhausted our search without finding the word
				return false;
			}

			//If we're doing a partial match, get our substring to compare against
			if($partialMatch === true)
			{
				$cmpWord = substr($this->wordlist[$min + $offset], 0, strlen($word));
			} else {
				$cmpWord = $this->wordlist[$min + $offset];
			}

			//Perform our string comparison
			$cmp = strcmp($cmpWord, $word);
			if($cmp == 0)
			{
				//We found it!
				return true;
			} elseif($cmp < 0) {
				//Our target word is in the top half
				$min += $offset;
			} elseif($cmp > 0) {
				//Our target word is in the bottom half
				$max = $min + $offset;
			}
		}

		//If we fall through, we've exhausted our search without finding a match
		return false;
	}

	/**
	 * processFile 
	 * Loads the file from disk and parses it into the internal wordlist array
	 * 
	 * @param mixed $file 
	 * @access private
	 * @return void
	 */
	private function processFile($file)
	{
		$this->wordlist = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		//sort($this->wordlist, SORT_STRING);
		$this->wordcount = count($this->wordlist);
	}
}

/**
 * Boggle 
 * This class takes in a Boggle board as a string and then can be
 * used to find all words on the board per regular Boggle rules.
 * 
 * @author Travis Veazey
 */
class Boggle
{
	private $board;
	private $dictionary;
	private $wordlist;
	private $score;

	/**
	 * __construct 
	 * Constructor for the Boggle class
	 * 
	 * @param string $input The boggle board as a string in left-right order
	 * @access public
	 * @return void
	 */
	public function __construct($input)
	{
		//Strip the incoming qu down to just q
		//We'll add the 'u' back on as we go along
		$input = str_replace('qu', 'q', strtolower($input));

		//Split up the board into the 4 separate rows
		$this->board = str_split($input, 4);

		//Get ourselves a dictionary
		$this->dictionary = Dictionary::instance();

		//Initialize an empty word list
		$this->wordlist = array();

		//Initialize empty score
		$this->score = 0;
	}

	public function findWords()
	{
		for($x = 0; $x < 4; $x++)
		{
			for($y = 0; $y < 4; $y++)
			{
				$this->searchBoard('', $x, $y);
			}
		}
	}

	public function printFoundWords()
	{
		foreach($this->wordlist as $word)
		{
			echo $word . "\n";
		}
	}

	public function printBoard()
	{
		foreach($this->board as $row)
		{
			echo strtoupper($row)."\n";
		}
		echo "\n";
	}

	public function printScore()
	{
		echo "This list of ".count($this->wordlist)." words is worth ".$this->score." points\n";
	}

	/**
	 * wordIsValid 
	 * Checks if the given word is valid per Boggle rules, specifically:
	 *  1. Checks that the word is at least 3 letter long
	 *  2. Checks that the word does not exist in our word list
	 *  3. Checks that the wrod does exist in our dictionary
	 * This method does not check that a word is too long - it assumes other mechanics
	 * will prevent that.
	 * 
	 * @param string $word The word to check
	 * @access private
	 * @return bool True if the word is a valid Boggle word, false otherwise
	 */
	private function wordIsValid($word)
	{
		//Only call strlen() once
		$len = strlen($word);

		//Words less than 3 letters are invalid in Boggle
		if($len < 3)
		{
			return false;
		}

		//It's valid per the rules, make sure we don't already have it
		if(in_array($word, $this->wordlist) === true)
		{
			return false;
		}

		//We haven't already found it - is it in our dictionary?
		return $this->dictionary->findWord($word);
	}

	/**
	 * wordCanBeValid 
	 * Makes sure there is at least one word in our dictionary with the given
	 * string at the beginning
	 * @see Dictionary::findWord
	 * 
	 * @param mixed $word The substring to search for
	 * @access private
	 * @return bool True is the string was found, false otherwise
	 */
	private function wordCanBeValid($word)
	{
		return $this->dictionary->findWord($word, true);
	}

	/**
	 * searchBoard 
	 * Recusrively search the board using a depth-first search, starting at
	 * the given (x,y) coordinates. The first parameter is the string built
	 * using the previously-visited cubes; the next two parameters indicate
	 * which cube is being checked in this iteration; the final parameter is
	 * an array noting which cubes have already been visited. This function
	 * recursively calls itself until it has searched all cubes or it has
	 * built a string that cannot possibly be a word (@see wordCanBeValid).
	 * 
	 * @param string $word The word built so far
	 * @param int $x The X index of the cube to add
	 * @param int $y The Y index of the cube to add
	 * @param array $visited An array containing boolean true at each (x,y) already visited
	 * @access private
	 * @return void
	 */
	private function searchBoard($word = '', $x = 0, $y = 0, $visited = array())
	{
		//abort if we've already visited this cube
		if($visited[$x][$y] === true)
		{
			return;
		}

		//validate $x and $y
		if($x > 3 || $x < 0 || $y > 3 || $y < 0)
		{
			return;
		}

		//first add the current letter to our word, and mark it visited
		$word .= $this->board[$x][$y];
		$visited[$x][$y] = true;

		//special case: append a 'u' if this letter is a 'q'
		if($this->board[$x][$y] == 'q')
		{
			$word .= 'u';
		}

		//add our word to our wordlist if it's valid
		if($this->wordIsValid($word))
		{
			$this->wordlist[] = $word;
			//bubbleInsert($this->wordlist, $word);
			$this->scoreWord($word);
		} else {
			//check if this can be a valid word
			//this check is here because wordCanBeValid will always return true if wordIsValid does
			if($this->wordCanBeValid($word) === false)
			{
				return;
			}
		}

		//now recursively call this function with each adjacent letter
		for($delta_x = -1; $delta_x <= 1; $delta_x++)
		{
			for($delta_y = -1; $delta_y <= 1; $delta_y++)
			{
				$this->searchBoard($word, $x + $delta_x, $y + $delta_y, $visited);
			}
		}
	}

	private function scoreWord($word)
	{
		$len = strlen($word);
		if($len >= 8)
		{
			$this->score += 11;
		} elseif($len >= 7) {
			$this->score += 5;
		} elseif($len >= 6) {
			$this->score += 3;
		} elseif($len >= 5) {
			$this->score += 2;
		} elseif($len >= 3) {
			$this->score += 1;
		}
	}
}

//Try to ensure we have enough memory
ini_set('memory_limit', '128M');

$board = new Boggle($argv[1]);
$board->findWords();
$board->printFoundWords();
$board->printScore();

