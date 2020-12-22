# blackjack PHP

This is a project made just for fun and trying to explore the world of gambling as well as creating a "Gaming Library" in PHP

## Philosophy

My general idea is to create a "GamesManager" which will be able to deal multiple types of cards and card games. By making it modular I hope to use as few compontents as possible to deal cards for games such as Spider, Three Card Poker, Blackjack & Patience.. more may come as ideas flow and depending on poupular demand

In later state even adding card games with other set of cards and rules like UNO, Cards Against Humanity & such that may be played at social gatherings


## Usage
  Require PHP-file in your document
  Initialize with
  ```
  $Games = new TMSE\GamesManager();
  $Games->dealBlackjack([number of decks to use]);
  ```

## TODO
- [ ] Add betting
###### Currency to be used to make it interesting
- [ ] Add "Double Down"
###### Player gets one additional card only and plays the hand
- [ ] Add ability to keep playing with the same set/deck(s) of cards
###### Making it possible to "count cards"
- [ ] Multiplayer
###### On the same device due to limitations, better alternatives & just not worth it
### Frontend
- [ ] Make it possible for player to set number of decks to be used
###### AJAX Request
- [ ] Publish frontend
###### Due to licensing no frontend provided
