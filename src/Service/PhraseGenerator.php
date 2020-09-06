<?php


namespace App\Service;


class PhraseGenerator
{
    private $formatting_variables = [];

    private function get_word($key)
    {
        if (isset($this->formatting_variables[$key])) {
            $words = $this->formatting_variables[$key];

            $w_max = count($words) - 1;
            $w_rand = rand(0, $w_max);
            // return the word, and check if the word contains another variable:
            return $this->replace_words(trim($words[$w_rand]));
        } else {
            return "(Error: Word '$key' was not found!)";
        }
    }

    private function replace_words($sentence)
    {
        // checks does sentence has any variables, if not returns the same sentence
        if (str_replace('%', '', $sentence) == $sentence)
            return $sentence;

        $words = explode(" ", $sentence);

        $new_sentence = array();

        // go trough all words:
        for ($w = 0; $w < count($words); $w++) {
            $word = trim($words[$w]);
            if ($word != '') {
                // is this word a variable?
                if (preg_match('/^%(.*)$/', $word, $m)) {
                    // --> yes
                    $varkey = trim($m[1]);
                    // get word from the variable list:
                    $new_sentence[] = $this->get_word($varkey);
                } else {
                    $new_sentence[] = $word;
                }
            }
        }
        // join the array to a new sentence:
        return implode(" ", $new_sentence);
    }

    /**
     * @param string $sentences Pass the blueprint of sentences
     * @param string $variables_for_sentences Pass variables whose will be added to the sentences blueprint
     * @return string Returns sentence
     */
    public function generate(string $sentences, string $variables_for_sentences):string
    {
        // strip spaces:
        $sentences = trim($sentences);
        $variables_for_sentences = trim($variables_for_sentences);

        // fix new lines and split sentences up:
        $sentences = str_replace("\r\n", "\n", $sentences);
        $sentences = str_replace("\r", "\n", $sentences);
        $sentences = explode("\n", $sentences);

        $variables_for_sentences = str_replace("\r\n", "\n", $variables_for_sentences);
        $variables_for_sentences = str_replace("\r", "\n", $variables_for_sentences);
        $variables_for_sentences = explode("\n\n", $variables_for_sentences);

        // go trough all variables:
        for ($x = 0; $x < count($variables_for_sentences); $x++) {
            $var = explode("\n", trim($variables_for_sentences[$x]));
            // lowecase all:
            $key = strtolower(trim($var[0]));
            // split words:
            $words = explode(";", trim($var[1]));
            // add variables to the $r_vars Array
            $this->formatting_variables[$key] = $words;
        }

        // calculate random number to take from the array
        $max_s = count($sentences) - 1;
        $rand_s = rand(0, $max_s);

        // get a random sentence:
        $sentence = $sentences[$rand_s];

        $sentence = str_replace(' ,',',', ucfirst($this->replace_words($sentence)));
        $sentence = str_replace(' .', '.', $sentence);
        $sentence = str_replace(' !', '!', $sentence);
        $sentence = str_replace(' ?', '?', $sentence);
        $sentence = trim($sentence);

        return $sentence;
    }
}