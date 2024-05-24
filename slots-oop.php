<?php

class SlotMachine
{
    private int $rows;
    private int $columns;
    private array $possibleBets = [10, 5, 80, 40, 20];
    private array $gameElements = ["A", "A", "A", "A", "A", "B", "B", "B", "B", "C", "C", "C", "D", "D", "7"];
    private array $board = [];
    private array $winningPositions = [
        [[0, 0], [0, 1], [0, 2]],
        [[1, 0], [1, 1], [1, 2]],
        [[2, 0], [2, 1], [2, 2]],
        [[0, 0], [1, 0], [2, 0]],
        [[0, 1], [1, 1], [2, 1]],
        [[0, 2], [1, 2], [2, 2]],
        [[0, 0], [1, 1], [2, 2]],
        [[0, 2], [1, 1], [2, 0]]
    ];

    public function __construct(int $rows = 3, int $columns = 3)
    {
        $this->rows = $rows;
        $this->columns = $columns;
    }

    private function calculateMultipliers(): array
    {
        $elementsRarity = array_count_values($this->gameElements);
        $elementsMultipliers = [];

        $minimumOccurrence = min($elementsRarity);

        foreach ($elementsRarity as $element => $count) {
            $elementsMultipliers[$element] = ($minimumOccurrence / $count) * 10;
        }

        return $elementsMultipliers;
    }

    private function getUserBet(int $userBalance): int
    {
        while (true) {
            $userBet = readline("Enter your bet amount (or 0 to exit):");

            if ($userBet === 0) return $userBet;

            if (!in_array($userBet, $this->possibleBets)) {
                echo "Please enter a valid bet - ";
                foreach ($this->possibleBets as $bet) {
                    echo "$bet points ";
                }
                continue;
            }

            if ($userBet > $userBalance) {
                echo "Not enough coins!";
                continue;
            }

            return $userBet;
        }
    }

    private function createBoard(): void
    {
        for ($row = 0; $row < $this->rows; $row++) {
            for ($column = 0; $column < $this->columns; $column++) {
                $this->board[$row][$column] = $this->gameElements[array_rand($this->gameElements)];
            }
        }
    }

    private function displayBoard(): void
    {
        foreach ($this->board as $row) {
            foreach ($row as $element) {
                echo "[ $element ] ";
            }
            echo PHP_EOL;
        }
    }

    private function calculateWin(int $userBet, array $elementsMultipliers): int
    {
        $winTotal = 0;

        foreach ($this->winningPositions as $position) {
            [$first, $_, $_] = $position;
            [$firstRow, $firstCol] = $first;

            $element = $this->board[$firstRow][$firstCol];

            $winPositionCounter = 0;

            foreach ($position as $coordinate) {
                if ($element === $this->board[$coordinate[0]][$coordinate[1]]) {
                    $winPositionCounter++;
                }
            }

            if ($winPositionCounter === count($position)) {
                $minimumBet = min($this->possibleBets);
                $betMultiplier = $userBet / $minimumBet;

                $elementMultiplier = $elementsMultipliers[$element];

                $win = $winPositionCounter * $elementMultiplier * $betMultiplier;
                $winTotal += $win;

                echo "Winning combo! - you get $win credits" . PHP_EOL;
            }

        }

        return $winTotal;
    }

    public function startGame(): void
    {
        $elementsMultipliers = $this->calculateMultipliers();

        $userBalance = (int)readline("Enter your amount of coins:");

        while ($userBalance >= min($this->possibleBets)) {
            $userBet = $this->getuserBet($userBalance);

            if ($userBet == 0) break;

            $this->createBoard();
            $this->displayBoard();

            $winTotal = $this->calculateWin($userBet, $elementsMultipliers);

            $userBalance = $userBalance - $userBet + $winTotal;
            echo "Your current balance - $userBalance credits" . PHP_EOL;
        }
    }
}

$slot = new SlotMachine(3, 3);
$slot->startGame();