<?php

class SudokuCalculator implements Observer
{

    private $sudoku;
    private $hasChanged;

    function __construct(Sudoku $sudoku)
    {
        $this->sudoku = clone $sudoku;
        foreach ($this->sudoku->getCells() as $cell) {
            if ($cell instanceof Cell) {
                $cell->addObserver($this);
            }
        }
        $this->hasChanged = true;
    }

    function calculate()
    {
        while ($this->hasChanged) {
            $this->hasChanged = false;
            $this->assignSimple();
            $this->assignByPossibilityHorizontal();
            $this->assignByPossibilityVertical();
            $this->assignByPossibilityQuadrant();
        }
    }

    private function assignSimple()
    {
        foreach ($this->sudoku->getCells() as $cell) {
            $cell->setValueIfUnique();
        }
    }

    private function assignByPossibilityHorizontal()
    {
        foreach ($this->sudoku->getCells() as $cell) {
            $allPossibilities = PossibilitiesCalculator::getHorizontal($this->sudoku, $cell);
            foreach ($cell->getPossibility()->getPossibilities() as $possibility) {
                if ($allPossibilities->notExists($possibility)) {
                    $cell->setValue($possibility);
                    return;
                }
            }
        }
    }

    private function assignByPossibilityVertical()
    {
        foreach ($this->sudoku->getCells() as $cell) {
            $allPossibilities = PossibilitiesCalculator::getVertical($this->sudoku, $cell);
            foreach ($cell->getPossibility()->getPossibilities() as $possibility) {
                if ($allPossibilities->notExists($possibility)) {
                    $cell->setValue($possibility);
                    return;
                }
            }
        }
    }

    private function assignByPossibilityQuadrant()
    {
        foreach ($this->sudoku->getCells() as $cell) {
            $allPossibilities = PossibilitiesCalculator::getQuadrant($this->sudoku, $cell);
            foreach ($cell->getPossibility()->getPossibilities() as $possibility) {
                if ($allPossibilities->notExists($possibility)) {
                    $cell->setValue($possibility);
                    return;
                }
            }
        }
    }

    function update(Observable &$observable)
    {
        $this->hasChanged = true;
    }

    public function getSudoku():Sudoku
    {
        return $this->sudoku;
    }

}