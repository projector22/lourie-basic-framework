<?php

namespace LBF\Tools\Validations;

/**
 * Tool for performing various common validations.
 * 
 * Use LBF\Tools\Validations\Validations;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.3.2-beta
 */

class Validations {


    /**
     * Validate if an ID number is valid or not.
     * 
     * @param   string  $id_number  The number to validate.
     * @param   string  $gender     A defined gender to validate against.
     *                              Should be parsed as `male` or `female`.
     * @param   integer $foreigner  A defined foreigner status to validate against. 
     *                              Should be parsed as `0` for a ZA citizen, `1` for a foreigner.
     * 
     * @return  boolean
     * 
     * Credit to this site:
     * @see     https://codeblock.co.za/how-to-validate-a-south-african-id-number-with-php/
     * 
     * @static
     * @access  public
     * @since   LBF 0.3.2-beta
     */

    public static function za_id_validation( string $id_number, string $gender = '', int $foreigner = 0 ): bool {
        $validated = false;
        if ( is_numeric( $id_number ) && strlen( $id_number ) === 13 ) {
            $errors = false;
            $num_array = str_split( $id_number );

            // Validate the day and month
            $id_month = $num_array[2] . $num_array[3];
            $id_day = $num_array[4] . $num_array[5];


            if ( $id_month < 1 || $id_month > 12 ) {
                $errors = true;
            }

            if ( $id_day < 1 || $id_day > 31 ) {
                $errors = true;
            }

            // Validate gender
            $id_gender = $num_array[6] >= 5 ? 'male' : 'female';
            if ( $gender && strtolower($gender) !== $id_gender ) {
                $errors = true;
            }

            // Validate citizenship

            // citizenship as per id number
            $id_foreigner = $num_array[10];

            // citizenship as per submission
            if ( ( $foreigner || $id_foreigner ) && (int)$foreigner !== (int)$id_foreigner ) {
                 $errors = true;
            }

            /**********************************
                Check Digit Verification
            **********************************/

            // Declare the arrays
            $even_digits = [];
            $odd_digits = [];

            // Loop through modified $num_array, storing the keys and their values in the above arrays
            foreach ( $num_array as $index => $digit ) {
                if ($index === 0 || $index % 2 === 0) {
                    $odd_digits[] = $digit;
                } else {
                    $even_digits[] = $digit;
                }
            }

            // use array pop to remove the last digit from $odd_digits and store it in $check_digit
            $check_digit = array_pop( $odd_digits );

            //All digits in odd positions (excluding the check digit) must be added together.
            $added_odds = array_sum( $odd_digits );

            //All digits in even positions must be concatenated to form a 6 digit number.
            $concatenated_evens = implode( '', $even_digits );

            //This 6 digit number must then be multiplied by 2.
            $evensx2 = $concatenated_evens * 2;

            // Add all the numbers produced from the even numbers x 2
            $added_evens = array_sum( str_split( $evensx2 ) );

            $sum = $added_odds + $added_evens;

            // get the last digit of the $sum
            $last_digit = substr( $sum, -1 );

            /* 10 - $last_digit
             * $verify_check_digit = 10 - (int)$last_digit; (Will break if $last_digit = 0)
             * Edit suggested by Ruan Luies
             * verify check digit is the resulting remainder of
             *  10 minus the last digit divided by 10
             */
             $verify_check_digit = ( 10 - (int)$last_digit ) % 10;

            // test expected last digit against the last digit in $id_number submitted
            if ( (int)$verify_check_digit !== (int)$check_digit ) {
                $errors = true;
            }

            // if errors haven't been set to true by any one of the checks, we can change verified to true;
            if ( !$errors ) {
                $validated = true;
            }
        }
        return $validated;
    }


    /**
     * Validate an email address.
     * 
     * @see https://www.php.net/manual/en/filter.examples.validation.php
     * 
     * @param   mixed   $email  A bit of data to validate as a valid email address
     * 
     * @return  boolean
     * 
     * @static
     * @access  public
     * @since   LBF 0.4.2-beta
     */

    public static function validate_email( mixed $email ): bool {
        if ( is_string( $email ) ) {
            if ( str_contains( $email, "\n" ) || str_contains( $email, "\r" ) ) {
                return false;
            }
        }
        return filter_var( $email, FILTER_VALIDATE_EMAIL ) !== false;
    }

}