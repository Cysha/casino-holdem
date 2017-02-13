# Left to Act

# Normal - 4 players

```
ButtonPos = 0;
L2A = [
    player2|STILL_TO_ACT
    player3|STILL_TO_ACT
    player4|STILL_TO_ACT
    player1|STILL_TO_ACT
];
```

```
player2->sb;
player3->bb;
```

```
player4->calls;
player1->folds;
player2->calls;
player3->checks;
```

```
L2A = [
    player4|ACTIONED
    player2|ACTIONED
    player3|ACTIONED
];
```

--------------------------------

```
round->dealFlop();
    # reset all actions on L2A
    # sortBy seat number
    # reset list to be @ (buttonPos + 1)
```

```
L2A = [
    player2|STILL_TO_ACT
    player3|STILL_TO_ACT
    player4|STILL_TO_ACT
];
```

```
player2->checks;
player3->raises;
player4->calls;
player2->folds;
```

```
L2A = [
    player3|AGGRESSIVELY_ACTIONED
    player4|ACTIONED
];
```

--------------------------------

```
round->dealTurn();
    # reset all actions on L2A
    # sortBy seat number
    # reset list to have small blind first
```

```
L2A = [
    player3|STILL_TO_ACT
    player4|STILL_TO_ACT
];
```


1 2 3 4
2 3 4 1

=< seatNo => * 10
 1 20 30 40
