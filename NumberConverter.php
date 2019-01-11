<?php

/**
 * Copyright @ 2019, All rights reserverd.
 * Author : Bobby Brillian Yerikho
 * Email : bobby.brillian@gmail.com
 * See LICENSE.txt for license details.
 */

class NumberConverter
{
    /**
     * @var array
     */
    protected $specialBreakIndex = [];

    /**
     * @param $value
     * @return string
     */
    public function encoder($value)
    {
        $isMinus = false;
        if ($value < 0) {
            $isMinus = true;
        }
        $value = abs($value);
        $words = array(
            "",
            "satu",
            "dua",
            "tiga",
            "empat",
            "lima",
            "enam",
            "tujuh",
            "delapan",
            "sembilan",
            "sepuluh",
            "sebelas"
        );
        $result = "";
        if ($value < 12) {
            $result = " " . $words[$value];
        } else {
            if ($value < 20) {
                $result = $this->encoder($value - 10) . " belas";
            } else {
                if ($value < 100) {
                    $result = $this->encoder($value / 10) . " puluh" . $this->encoder($value % 10);
                } else {
                    if ($value < 200) {
                        $result = " seratus" . $this->encoder($value - 100);
                    } else {
                        if ($value < 1000) {
                            $result = $this->encoder($value / 100) . " ratus" . $this->encoder($value % 100);
                        } else {
                            if ($value < 2000) {
                                $result = " seribu" . $this->encoder($value - 1000);
                            } else {
                                if ($value < 1000000) {
                                    $result = $this->encoder($value / 1000) . " ribu" . $this->encoder($value % 1000);
                                } else {
                                    if ($value < 1000000000) {
                                        $result = $this->encoder($value / 1000000) . " juta" . $this->encoder($value % 1000000);
                                    } else {
                                        if ($value < 1000000000000) {
                                            $result = $this->encoder($value / 1000000000) . " milyar" . $this->encoder(fmod($value,
                                                    1000000000));
                                        } else {
                                            if ($value < 1000000000000000) {
                                                $result = $this->encoder($value / 1000000000000) . " trilyun" . $this->encoder(fmod($value,
                                                        1000000000000));
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($isMinus) {
            $result = "minus " . $result;
        }

        return $result;
    }

    /**
     * @param $value
     * @return int|null
     */
    public function decoder($value)
    {
        $result = null;
        $numberTemp = null;
        $numberList = $this->getNumberList();

        $arrValue = explode(" ", $value);
        $words = $this->getCleanWords($arrValue);
        $isValid = $this->validateWords($words);
        if ($isValid) {
            $this->clearSpecialBreakIndex();
            foreach ($words as $key => $word) {
                if (isset($numberList[$word])) {
                    if (is_null($numberTemp)) {
                        $numberTemp = $numberList[$word];
                    } else {
                        $numberTemp += $numberList[$word];
                    }
                } else {
                    if ($this->isBreakpoint($word)) {
                        switch ($word) {
                            case "puluh":
                                $numberTemp = $numberTemp * 10;
                                break;
                            case "ratus":
                                $numberTemp = $numberTemp * 100;
                                break;
                            case "ribu":
                                $numberTemp = $numberTemp * 1000;
                                break;
                            case "juta":
                                $numberTemp = $numberTemp * 1000000;
                                break;
                        }
                        $result = $this->countResult($result, $numberTemp, $words, $key);
                        $numberTemp = $this->countTemp($numberTemp, $words, $key);
                    } else {
                        if (strpos($word, "belas") !== false) {
                            if (strpos($word, "se") !== false) {
                                if (is_null($numberTemp)) {
                                    $numberTemp = 11;
                                } else {
                                    $numberTemp += 11;
                                }
                            } else {
                                if (isset($words[$key - 1])) {
                                    $prevWord = $words[$key - 1];
                                    if (isset($numberList[$prevWord])) {
                                        $wordTemp = "1" . strval($numberList[$prevWord]);
                                        if (is_null($numberTemp)) {
                                            $numberTemp = intval($wordTemp);
                                        } else {
                                            $numberTemp -= intval($numberList[$prevWord]);
                                            $numberTemp += intval($wordTemp);
                                        }
                                    } else {
                                        $isValid = false;
                                    }
                                } else {
                                    $isValid = false;
                                }
                            }
                            $result = $this->countResult($result, $numberTemp, $words, $key);
                            $numberTemp = $this->countTemp($numberTemp, $words, $key);
                        } elseif (strpos($word, "sepuluh") !== false) {
                            if (is_null($numberTemp)) {
                                $numberTemp = 10;
                            } else {
                                $numberTemp += 10;
                            }
                            $result = $this->countResult($result, $numberTemp, $words, $key);
                            $numberTemp = $this->countTemp($numberTemp, $words, $key);
                        } elseif (strpos($word, "seratus") !== false) {
                            if (is_null($numberTemp)) {
                                $numberTemp = 100;
                            } else {
                                $numberTemp += 100;
                            }
                            $result = $this->countResult($result, $numberTemp, $words, $key);
                            $numberTemp = $this->countTemp($numberTemp, $words, $key);
                        } elseif (strpos($word, "seribu") !== false) {
                            if (is_null($numberTemp)) {
                                $numberTemp = 1000;
                            } else {
                                $numberTemp += 1000;
                            }
                            $result = $this->countResult($result, $numberTemp, $words, $key);
                            $numberTemp = $this->countTemp($numberTemp, $words, $key);
                        } elseif (strpos($word, "sejuta") !== false) {
                            if (is_null($numberTemp)) {
                                $numberTemp = 1000000;
                            } else {
                                $numberTemp += 1000000;
                            }
                            $result = $this->countResult($result, $numberTemp, $words, $key);
                            $numberTemp = $this->countTemp($numberTemp, $words, $key);
                        }
                    }
                }
            }
            if ($numberTemp) {
                $result += $numberTemp;
            }
        } else {
            $result = "invalid";
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getValidPattern()
    {
        $pattern = [
            "satu",
            "dua",
            "tiga",
            "empat",
            "lima",
            "enam",
            "tujuh",
            "delapan",
            "sembilan",
            "sepuluh",
            "sebelas",
            "seratus",
            "seribu",
            "sejuta",
            "puluh",
            "belas",
            "ratus",
            "ribu",
            "juta"
        ];
        return $pattern;
    }

    /**
     * @return array
     */
    public function getInvalidPattern()
    {
        $pattern = [
            ["belas", "ratus"],
            ["puluh", "ratus"],
            ["juta", "ratus"],
            ["belas", "puluh"],
            ["puluh", "puluh"],
            ["juta", "puluh"],
            ["ratus", "ratus"],
            ["juta", "juta"],
            ["belas", "belas"],
            ["ribu", "ratus"],
            ["ribu", "puluh"],
            ["ribu", "ribu"],
            ["ribu", "belas"],
            ["ribu", "ratus"],
            ["ribu", "juta"],
            ["juta", "ribu"],
            ["satu", "satu"],
            ["satu", "dua"],
            ["satu", "tiga"],
            ["satu", "empat"],
            ["satu", "lima"],
            ["satu", "enam"],
            ["satu", "tujuh"],
            ["satu", "delapan"],
            ["satu", "sembilan"],
            ["dua", "satu"],
            ["dua", "dua"],
            ["dua", "tiga"],
            ["dua", "empat"],
            ["dua", "lima"],
            ["dua", "enam"],
            ["dua", "tujuh"],
            ["dua", "delapan"],
            ["dua", "sembilan"],
            ["tiga", "satu"],
            ["tiga", "dua"],
            ["tiga", "tiga"],
            ["tiga", "empat"],
            ["tiga", "lima"],
            ["tiga", "enam"],
            ["tiga", "tujuh"],
            ["tiga", "delapan"],
            ["tiga", "sembilan"],
            ["empat", "satu"],
            ["empat", "dua"],
            ["empat", "tiga"],
            ["empat", "empat"],
            ["empat", "lima"],
            ["empat", "enam"],
            ["empat", "tujuh"],
            ["empat", "delapan"],
            ["empat", "sembilan"],
            ["lima", "satu"],
            ["lima", "dua"],
            ["lima", "tiga"],
            ["lima", "empat"],
            ["lima", "lima"],
            ["lima", "enam"],
            ["lima", "tujuh"],
            ["lima", "delapan"],
            ["lima", "sembilan"],
            ["enam", "satu"],
            ["enam", "dua"],
            ["enam", "tiga"],
            ["enam", "empat"],
            ["enam", "lima"],
            ["enam", "enam"],
            ["enam", "tujuh"],
            ["enam", "delapan"],
            ["enam", "sembilan"],
            ["tujuh", "satu"],
            ["tujuh", "dua"],
            ["tujuh", "tiga"],
            ["tujuh", "empat"],
            ["tujuh", "lima"],
            ["tujuh", "enam"],
            ["tujuh", "tujuh"],
            ["tujuh", "delapan"],
            ["tujuh", "sembilan"],
            ["delapan", "satu"],
            ["delapan", "dua"],
            ["delapan", "tiga"],
            ["delapan", "empat"],
            ["delapan", "lima"],
            ["delapan", "enam"],
            ["delapan", "tujuh"],
            ["delapan", "delapan"],
            ["delapan", "sembilan"],
            ["sembilan", "satu"],
            ["sembilan", "dua"],
            ["sembilan", "tiga"],
            ["sembilan", "empat"],
            ["sembilan", "lima"],
            ["sembilan", "enam"],
            ["sembilan", "tujuh"],
            ["sembilan", "delapan"],
            ["sembilan", "sembilan"]
        ];
        return $pattern;
    }

    /**
     * @param $words
     * @return bool
     */
    protected function validateWords($words)
    {
        $isValid = true;
        $vPatterns = $this->getValidPattern();
        $patterns = $this->getInvalidPattern();
        foreach ($words as $key => $word) {
            if (!in_array($word, $vPatterns)) {
                return false;
            }

            if (isset($words[$key + 1])) {
                $nextWord = $words[$key + 1];
                foreach ($patterns as $pattern) {
                    if ($word == $pattern[0] && $nextWord == $pattern[1]) {
                        return false;
                    }
                }
            }
        }
        return $isValid;
    }

    /**
     * Save special break index
     */
    protected function clearSpecialBreakIndex()
    {
        $this->specialBreakIndex = [];
    }

    /**
     * @param $key
     */
    protected function saveSpecialBreakIndex($key, $type)
    {
        $this->specialBreakIndex[$key] = ["type" => $type];
    }

    /**
     * @param $array
     * @return array
     */
    protected function getCleanWords($array)
    {
        $words = [];
        foreach ($array as $word) {
            if (!empty($word)) {
                $words[] = $word;
            }
        }
        return $words;
    }

    /**
     * @return array
     */
    protected function getBreakpoints()
    {
        $breakPoints = [
            "puluh",
            "ratus",
            "ribu",
            "juta"
        ];

        return $breakPoints;
    }

    /**
     * @param $word
     * @return bool
     */
    protected function isBreakpoint($word)
    {
        $breakPoints = $this->getBreakpoints();
        $result = false;
        if (in_array($word, $breakPoints)) {
            $result = true;
        }
        return $result;
    }

    /**
     * @return array
     */
    protected function getNumberList()
    {
        $numberList = [
            "nol" => 0,
            "satu" => 1,
            "dua" => 2,
            "tiga" => 3,
            "empat" => 4,
            "lima" => 5,
            "enam" => 6,
            "tujuh" => 7,
            "delapan" => 8,
            "sembilan" => 9
        ];
        return $numberList;
    }

    /**
     * @param $word
     * @return bool
     */
    protected function isNumberBreak($word)
    {
        $numerList = $this->getNumberList();
        $result = false;
        if (isset($numerList[$word])) {
            $result = true;
        }
        return $result;
    }

    /**
     * @param $curResult
     * @param $tempCount
     * @param $words
     * @param $key
     * @return int
     */
    protected function countResult($curResult, $tempCount, $words, $key)
    {
        $result = $curResult;
        if (isset($this->specialBreakIndex[$key])) {
            if ($this->specialBreakIndex[$key]["type"] == "replace") {
                $this->specialBreakIndex[$key]["value"] = $curResult;
                $result = 0;
            } elseif ($this->specialBreakIndex[$key]["type"] == "replace2") {
                $newResult = $curResult / 1000;
                $this->specialBreakIndex[$key]["value"] = ($newResult + $tempCount) / 10;
                $result = 0;
            }
        } else {
            $isBreak = $this->isBreak($words, $key);
            if ($isBreak) {
                if (is_null($result)) {
                    $result = $tempCount;
                } else {
                    $result += $tempCount;
                }
            }
        }
        return $result;
    }

    /**
     * @param $curTemp
     * @param $words
     * @param $key
     * @return int
     */
    protected function countTemp($curTemp, $words, $key)
    {
        $temp = null;
        if (isset($this->specialBreakIndex[$key])) {
            if ($this->specialBreakIndex[$key]["type"] == "replace") {
                $temp = $curTemp + $this->specialBreakIndex[$key]["value"];
            } elseif ($this->specialBreakIndex[$key]["type"] == "replace2") {
                $temp = $this->specialBreakIndex[$key]["value"];
            }
        } else {
            $isBreak = $this->isBreak($words, $key);
            if (!$isBreak) {
                $temp = $curTemp;
            }
        }
        return $temp;
    }

    /**
     * @param $words
     * @param $key
     * @return bool
     */
    protected function isBreak($words, $key)
    {
        $curWord = $words[$key];
        $break = false;

        if (strpos($curWord, "juta") !== false) {
            $break = true;
            if (isset($words[$key + 1])) {
                $nextWord = $words[$key + 1];
                if (strpos($nextWord, "seratus") !== false) {
                    if (isset($words[$key + 3]) !== false) {
                        $next3Word = $words[$key + 3];
                        if ($next3Word == "puluh") {
                            $this->saveSpecialBreakIndex($key + 1, "replace2");
                        }
                    }
                } else {
                    $isNumberBreak = $this->isNumberBreak($nextWord);
                    if ($isNumberBreak) {
                        if (isset($words[$key + 2])) {
                            $next2Word = $words[$key + 2];
                            if (strpos($next2Word, "ratus") !== false) {
                                if (isset($words[$key + 4])) {
                                    $next4Word = $words[$key + 4];
                                    if ($next4Word == "puluh") {
                                        $this->saveSpecialBreakIndex($key + 2, "replace2");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if (isset($words[$key + 1])) {
                $nextWord = $words[$key + 1];
                $isNumberBreak = $this->isNumberBreak($nextWord);
                if ($isNumberBreak) {
                    $break = true;
                }
                if (isset($words[$key + 2])) {
                    $next2Word = $words[$key + 2];
                    if (strpos($curWord, "puluh") !== false || strpos($curWord, "ratus") !== false) {
                        $isBreakpoint = $this->isBreakpoint($next2Word);
                        if ($break) {
                            if (
                                ((strpos($next2Word, "ribu") !== false) && $isBreakpoint) ||
                                (strpos($next2Word, "belas") !== false) ||
                                (strpos($next2Word, "juta") !== false)
                            ) {
                                $break = false;
                            }
                        }
                    }

                    if (isset($words[$key + 3])) {
                        $next3Word = $words[$key + 3];
                        if (strpos($curWord, "ratus") !== false) {
                            $isBreakpoint2 = $this->isBreakpoint($next2Word);
                            $isBreakpoint3 = $this->isBreakpoint($next3Word);
                            if ($break) {
                                if (
                                ((strpos($next2Word, "puluh") !== false) &&
                                    $isBreakpoint2 &&
                                    $isBreakpoint3 &&
                                    ((strpos($next3Word, "ribu") !== false) || (strpos($next3Word, "juta") !== false))
                                )
                                ) {
                                    $this->saveSpecialBreakIndex($key + 2, "replace");
                                }
                            }
                        }
                    }

                    if (isset($words[$key + 4])) {
                        $next4Word = $words[$key + 4];
                        if (strpos($curWord, "ratus") !== false) {
                            $isBreakpoint2 = $this->isBreakpoint($next2Word);
                            $isBreakpoint4 = $this->isBreakpoint($next4Word);
                            if ($break) {
                                if (
                                ((strpos($next2Word, "puluh") !== false) &&
                                    $isBreakpoint2 &&
                                    $isBreakpoint4 &&
                                    ((strpos($next4Word, "ribu") !== false) || (strpos($next4Word, "juta") !== false))
                                )
                                ) {
                                    $this->saveSpecialBreakIndex($key + 2, "replace");
                                }
                            }
                        }
                    }
                }
            }
        }
        return $break;
    }
}