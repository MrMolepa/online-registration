<?php

function getAlphabets( $index, $index_column = false )
{
    $alphabets = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I',
        'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
        'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    ];

    return $alphabets[ $index ];
}

function getLowerAlphabets( $index, $index_column = false )
{
    $alphabets = [
        'a', 'b', 'c', 'd', 'e'
    ];

    return $alphabets[ $index_column ];
}

function getNumberic( $index, $index_column = false )
{
    return $index;
}

/**
 * array[
 *      'start_point_x' => float,
 *      'start_point_y' => float,
 *      'box_quantity'  => integer,
 *      'box_width'     => float,
 *      'box_height'    => float
 * ]
 */
function createBox( $pdf, $options )
{
    $start_point_x = $options['start_point_x'];
    $start_point_y = $options['start_point_y'];
    $box_quantity  = $options['box_quantity'];
    $box_width     = $options['box_width'];
    $box_height    = $options['box_height'];
    $border        = $options['border'];

    $pdf->SetXY( $start_point_x, $start_point_y );

    for( $i = 1; $i <= $box_quantity; $i++ )
    {
        if ( $i === $box_quantity )
            $pdf->Cell( $box_width, $box_height, '', $border['end'] );
        else
            $pdf->Cell( $box_width, $box_height, '', $border['start'] );
    }

    return $pdf;
}

function createCircle( $pdf, $options )
{
    $row    = $options[ 'length_row' ];
    $column = $options[ 'length_column' ];

    $start_point_x = $options[ 'start_point_x' ];
    $start_point_y = $options[ 'start_point_y' ];

    $circle_radius = $options[ 'circle_radius' ];

    $box_width  = $options[ 'box' ][ 'width' ];
    $box_height = $options[ 'box' ][ 'height' ];

    $circle_fill_function = $options['circle_fill_function'];

    $numbering = false;
    $numbering_options = [];

    if ( isset($options['numbering']) )
    {
        $numbering = true;
        $numbering_options = $options['numbering'];
    }

    $y = $start_point_y;
    for( $i = 0; $i < $row; $i++ )
    {
        $x  = $start_point_x;
        $y += 4.3;

        $pdf = createBox( $pdf, [
            'start_point_x' => $x + 2.35,
            'start_point_y' => $y - 2,
            'box_quantity'  => $column,
            'box_width'     => $box_width,
            'box_height'    => $box_height,
            'border'        => [
                'start' => 0,
                'end'   => 0,
            ],
        ]);

        $pdf->SetXY($x + .35, $y - 2);

        if ( $numbering )
        {
            $pdf->Text( $x - 2, $y + 0.9, $numbering_options['start_number'] . '.' );
            $numbering_options['start_number']++;
        }
        for ( $j = 0; $j < $column; $j++ )
        {
            $x += 4.7;

            $pdf->Circle( $x, $y, $circle_radius );
            $pdf->Cell( $box_width, $box_height + 0.5, $circle_fill_function($i, $j), 0, 0, 'C' );
        }
    }

    return $pdf;
}

function createBackgroundFill( $pdf, $options )
{
    $x_fill      = $options['x_fill'];
    $y_fill      = $options['y_fill'];
    $fill_height = $options['fill_height'];
    $fill_width  = $options['fill_width'];

    $fill_column_qty = $options['fill_column_qty'];
    $start_color = 0;
    if ( isset( $options['start_color'] ) ) $start_color = $options['start_color'];

    $fill_colors = [
        [230, 230, 230],
        [199, 219, 252]
    ];

    for( $i_fill = 0; $i_fill < $fill_column_qty; $i_fill++ )
    {
        $index_fill_color = ($i_fill + $start_color) % 2;
        $pdf->setFillColor( $fill_colors[$index_fill_color][0], $fill_colors[$index_fill_color][1], $fill_colors[$index_fill_color][2] );
        $pdf->SetXY( $x_fill, $y_fill );
        $pdf->Cell( $fill_width, $fill_height, '', 0, 0, 'L', 1 );

        $x_fill += $fill_width;
    }

    return $pdf;
}
