<?php 
namespace TMSE;

	use PDO;

class GamesManager extends Main {

	protected $DB;
	
	// Create arrays to be used 
	
	public $cards = [];
	public $types = [];
	//public $decks;

	public function __construct() {
		 //$this->decks = 1;
		 
		 // Possible cards and value
		 $this->cards = array('2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, 'T' => 10, 'J' => 10, 'Q' => 10, 'K' => 10, 'A' => 11);
		 $this->types = array('C', 'S', 'H', 'D');
		 
		 if (isset($_GET['stand'])) {
			// Player stands, dealer plays. Must stay at 17 if hit
			$dealerCount = self::calcCards($_SESSION['hand_computer']);
			while ($dealerCount < 17) {
				$newCard = self::getCard(1);
				array_push($_SESSION['hand_computer'], $newCard);
				$dealerCount = self::calcCards($_SESSION['hand_computer']);
			}
			
			// set to 1 if all dealer cards shall be shown
			$_SESSION['showDealerHands'] = 1;
			$_SESSION['stand'] = 1;
			//self::getWinner($_SESSION['hand_player'], $_SESSION['hand_computer']);
		}
		
		if (isset($_GET['hit'])) {
			// Give user card
			if (self::calcCards($_SESSION['hand_player']) > 21) { return ; }
			$newCard = self::getCard(1);
			array_push($_SESSION['hand_player'], $newCard);
			$_SESSION['turns']++;
			
			return '<img src="cards/'.$newCard.'.gif" alt="'.$newCard.'" />';
			//self::getWinner($_SESSION['hand_player'], $_SESSION['hand_computer']);
		}
		
		if (isset($_POST['double'])) {
			// Increase bet and give card
		}
		
		if (isset($_GET['fold'])) {
			echo "Folded!";
			$_SESSION['showDealerHands'] = 1;
			$_SESSION['stand'] = 1;
			self::displayCards($_SESSION['hand_computer'], $_SESSION['hand_player']);
			self::actionButtons();
			self::reset();
		}
	}
	
	public function pullCards($decks) {
		// $Joker is if Jokers should be added in set
		global $deckofCards;
		
		$deckofCards = array();
		 
		 // Create deck of 52
		 // $decks = Number of decks in play
		 
		 for ($i = 1; $i <= $decks; $i++) {
			foreach(array_keys($this->cards) as $card) {
				array_push($deckofCards, $card . "C", $card . "S", $card . "H", $card . "D");
			}
			shuffle($deckofCards);
		 }
	}
	
	public function getCard($number) {
		global $deckofCards;
		
		
		// Give random cards by amount
		// $number = How many cards
		
		
		// Get which cards hasn't been used or play whole deck(s)
		if (isset($_SESSION['cardsInPlay'])) {
			$dealSet = $_SESSION['cardsInPlay'];
		} else {
			$dealSet = $deckofCards;
		}
		
		
		for ($i = 1; $i <= $number; $i++) {
			$randomCard = array_rand(array_values($dealSet));
			return $dealSet[$randomCard];
			$key = array_search($dealSet[$randomCard], $dealSet);
			unset($dealSet[$key]);
		}
		$deckofCards = array_values($dealSet);
		
		
	}
	
	public function displayCards($hand1, $hand2) {
		$nrCard = 0;
		
		// Display cards visually
		// If first card by dealer show closed card
		
		
		$dealerCount = self::calcCards($hand1);
		
		echo '<div id="hand_dealer">';
		foreach($hand1 as $card) {
			if ($nrCard == 0 && $dealerCount != 21 && !isset($_SESSION['showDealerHands'])) {
				$closedCard = $card;
				echo '<img src="cards/closed.gif" alt="'.$card.'" />';
			} else {
				echo '<img src="cards/'.$card.'.gif" alt="'.$card.'" />';
			}
			$nrCard++;
		}
		if (isset($_SESSION['showDealerHands'])) {
			echo '<strong>';
			echo self::calcCards($hand1);
			echo '</strong>';
		}
		echo '</div>';
		
		echo '<br /><br /><br />';
		
		echo '<div id="hand_player">';
		foreach($hand2 as $card) {
			echo '<img src="cards/'.$card.'.gif" alt="'.$card.'" />';
		}
		echo '<strong>';
		echo self::calcCards($hand2);
		echo '</strong>';
		echo '</div>';
	}
	
	public function actionButtons() {
		
		echo '
		<div id="blackjackButtons">
		<input type="submit" id="hit" name="hit" class="btn btn-primary btn-rounded" value="Hit" />
		<input type="submit" id="stand" name="stand" class="btn btn-success btn-rounded" value="Stand" />
		<input type="submit" id="fold" name="fold" class="btn btn-outline-warning btn-rounded" value="Fold" />
		</div>
		<p>Dealer must stand at 17</p>
		';
		
	}
	
	
	public function calcCards($hand) {
		global $oountCards;
		
		
		// Calculate value of cards
		// If Ace makes count over 21
		// Ace value = 1
		// King, Queen & Jack = 10
		// T = number 10
		// Ex $card = 9C
		// $card = (value)(suit)
		
		$countCards = 0;
		
		foreach($hand as $card) {
			if (strpos($card, 'A') !== false) {
				$testCount = $countCards + 11;
				if ($testCount > 21) {
					$countCards += 1;
				} else {
					$countCards += 11;
				}
			} elseif (strpos($card, 'K') !== false) {
				$countCards += 10;
			} elseif (strpos($card, 'Q') !== false) {
				$countCards += 10;
			} elseif (strpos($card, 'J') !== false) {
				$countCards += 10;
			} elseif (strpos($card, 'T') !== false) {
				$countCards += 10;
			} else {
				// Strip suit from $card
				$val = substr($card, 0, 1);
				if (is_numeric($val)) {
				$countCards += $val;
				}
			}
		}
		
		// Output value of hand
		return $countCards;
	}
	
	public function getWinner($player, $dealer) {
		// value == calcCards
		
		// Initialize process to calculate play
		
		$player = self::calcCards($player);
		$dealer = self::calcCards($dealer);
		
		echo '<div id="message">';
		if ($player == 21) {
			// Win by Blackjack for Player
			$_SESSION['showDealerHands'] = 1;
			echo "Player nailed it";
		} elseif ($dealer == 21) {
			// Win by Blackjack for Dealer
			$_SESSION['showDealerHands'] = 1;
			echo "Dealer nailed it";
		} elseif ($_SESSION['stand'] == 1 && $dealer > $player && $dealer < 21) {
			$_SESSION['showDealerHands'] = 1;
			echo "Dealer wins";
		} elseif ($_SESSION['stand'] == 1 && $dealer < $player && $player < 21) {
			$_SESSION['showDealerHands'] = 1;
			echo "Player wins";
		} elseif ($dealer == $player) {
			$_SESSION['showDealerHands'] = 1;
			echo "Draw";
		} elseif ($player > 21) {
			// More than 21
			$_SESSION['showDealerHands'] = 1;
			echo "Player got more than 21";
		} elseif ($dealer > 21) {
			// Dealer lost
			$_SESSION['showDealerHands'] = 1;
			echo "Dealer too high";
		} elseif ($player > $dealer && $player <= 21 && $_SESSION['turns'] > 0 && $_SESSION['stand'] == 1) {
			$_SESSION['showDealerHands'] = 1;
			// Player wins
			echo "Player win last ordament";
		} elseif ($dealer > $player && $dealer <= 21 && $_SESSION['turns'] > 0 && $_SESSION['stand'] == 1) {
			$_SESSION['showDealerHands'] = 1;
			echo "Dealer win last ordament";
		} else {
			// No winner or draw, play keeps going
			echo "Keep fucking going";
		}
		echo '</div>';
		
	}

	public function dealBlackjack($decks) {
		global $deckofCards;
		
		// Initialize Blackjack
		
		echo '<section id="blackjack">';
		
		self::pullCards($decks);
		
		// If new game
		
		if (!$_SESSION['hand_player'] && !isset($_GET['fold'])) {
	
			$hand_player = array();
			$hand_computer = array();
	
			// Initial cards player
			array_push($hand_player, self::getCard(1));
			array_push($hand_player, self::getCard(1));
	
			// Initial cards computer
			array_push($hand_computer, self::getCard(1));
			array_push($hand_computer, self::getCard(1));
			
			
			// Cards currently used
			$cardsInPlay = array_merge($hand_player, $hand_computer);
			
			foreach($cardsInPlay as $card) {
				$key = array_search($card, $deckofCards);
				unset($deckofCards[$key]);
			}
			
	
			$_SESSION['hand_player'] = $hand_player;
			$_SESSION['hand_computer'] = $hand_computer;
			$_SESSION['cardsInPlay'] = $deckofCards;
			$_SESSION['usedCards'] = $cardsInPlay;
			$_SESSION['turns'] = 0;
			
			
			// Check if Blackjack occured from start
			self::getWinner($_SESSION['hand_player'], $_SESSION['hand_computer']);
			
			
			self::displayCards($hand_computer, $hand_player);
			
			self::actionButtons();
			
	
		} else {
			//self::reset();
			// If user refreshes page with current play & no action (fold)
			if (!isset($_GET['fold'])) {
				self::getWinner($_SESSION['hand_player'], $_SESSION['hand_computer']);
				
				self::displayCards($_SESSION['hand_computer'], $_SESSION['hand_player']);
			
				self::actionButtons();
			}
		}
		echo '</section>';
	}
	
	public function reset() {
		// Clear previous round
		unset($_SESSION['hand_player']);
		unset($_SESSION['hand_computer']);
		unset($_SESSION['cardsInPlay']);
		unset($_SESSION['usedCards']);
		unset($_SESSION['turns']);
		unset($_SESSION['showDealerHands']);
		unset($_SESSION['stand']);
	}
}
?>