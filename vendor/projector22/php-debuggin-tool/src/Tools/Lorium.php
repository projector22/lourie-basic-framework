<?php

namespace Debugger\Tools;

/**
 * Generator for creating lorium ipsum.
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   1.0.0
 */

class Lorium {
    /**
     * Display a block of text on the screen for demo or testing purposes
     * 
     * @param   int     $count  The number of times to perform this action
     *                          Default: 1
     * 
     * @access  public
     * @since   1.0.0
     */

    public function generate( int $count = 1 ): void {
        for ( $i = 0; $i < $count; $i++ ) {
            echo "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc id suscipit lorem. Aliquam erat volutpat. Quisque congue dapibus pulvinar. Maecenas viverra elementum velit. Suspendisse ullamcorper quis tortor sed aliquet. Vestibulum congue ligula semper arcu euismod egestas. Ut vel placerat sapien, sit amet lacinia nibh. Duis egestas orci nec est rutrum elementum. Suspendisse vel vulputate dolor. Donec dapibus lorem eget diam ornare, vitae feugiat nunc mattis. Curabitur vel congue mi, ut iaculis urna. Nullam non quam ultricies, pulvinar lorem sed, cursus nulla. Sed id vehicula leo. Cras ante massa, sagittis consectetur ipsum in, aliquet cursus dolor. Nulla facilisi. In quis molestie lorem.</p>
            <p>Suspendisse porta sollicitudin dolor non tincidunt. Duis eget vulputate ipsum, eu tincidunt justo. Ut eget tincidunt orci. Suspendisse ac quam et nulla interdum imperdiet non at ante. Aenean condimentum nec nisl vitae faucibus. Nam diam elit, finibus vel quam vel, luctus ultrices tellus. Phasellus in lorem vitae nisl gravida bibendum eget in ante. Sed maximus venenatis maximus. Aliquam rutrum, leo sed dignissim commodo, nisl nisi ultrices tellus, sed tempus urna tortor vel ante. Aliquam congue orci id tortor elementum, at tempus nulla egestas. Nunc mattis lacus id odio mollis, vitae lacinia massa ornare. Ut ultricies felis lacus, et mollis nunc eleifend sit amet. Quisque arcu sem, faucibus eu dolor in, pretium tempor dolor. Duis convallis auctor mi, in accumsan nibh rhoncus eget. Mauris at est libero.</p>
            <p>Cras justo mi, fringilla quis arcu sed, viverra congue quam. Duis rhoncus metus diam, quis fermentum leo dignissim eget. Nullam fringilla enim ac turpis vulputate dapibus. Aliquam quis dolor sapien. Duis a arcu mauris. Ut eget erat sagittis, efficitur mi sed, porta justo. Fusce porta purus convallis eleifend tempor. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin vitae iaculis lacus. Sed in eleifend est. Quisque id nunc porttitor, accumsan massa in, sagittis elit. Phasellus auctor viverra iaculis.</p>
            <p>Etiam sed orci orci. Fusce semper leo vel ullamcorper vulputate. Duis id nibh eu dui sagittis malesuada. Mauris magna tortor, facilisis vitae augue nec, efficitur imperdiet magna. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Suspendisse condimentum quis sem at porta. Duis non interdum nulla. Cras sit amet eleifend sapien. Nunc tellus enim, aliquam eget felis quis, viverra viverra mi. Integer pharetra libero arcu, vehicula scelerisque erat volutpat eget. Nam malesuada leo sed nulla imperdiet, eget vestibulum lorem maximus. Nam placerat, elit at suscipit tincidunt, dolor leo ultricies neque, in ultricies justo massa vel libero. Duis eu risus id nisl ullamcorper pellentesque et a nunc. Maecenas luctus nibh non est rhoncus, et euismod ligula dictum.</p>
            <p>Nam id posuere urna. Aenean velit justo, aliquet nec mauris sed, placerat vehicula justo. Cras et urna id ligula blandit commodo. Sed sed quam ornare, condimentum ex nec, accumsan justo. Donec id auctor mauris. Morbi accumsan mi nec lorem faucibus faucibus. Duis suscipit fringilla tellus id efficitur. Etiam ultrices enim vitae arcu posuere, ac auctor urna volutpat. Cras vel porta metus, quis porttitor sapien. Duis maximus, magna et porta tempor, libero augue ultrices libero, quis ultrices eros turpis at tortor. Aliquam imperdiet malesuada risus aliquam rutrum.</p>";
            echo "<br><br>";
        }
    }
}