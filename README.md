boggle-solver
=============

Given a Boggle board, this script finds all possible words and scores the results

boggle.php
----------

Usage is simple:
```
php boggle.php board
```
Where board is the Boggle board, listed on a single line in left-to-right, top-
to-bottom order. Keep in mind that in Boggle, the 'Q' is always followed by an
extra 'u'.

dice.php
--------

This script outputs a Boggle board in exactly the format boggle.php expects. To
test the solver on a random board, you can run this command:
```
php boggle.php `php dice.php`
```
Alternatively, you can run dice.php separately if you want to try and play the
board yourself, then feed it into boggle.php to check your score against the
maximum (as well as validate your words).
