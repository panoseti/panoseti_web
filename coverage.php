<?php

require_once("panoseti.inc");

// show a "heat map"
// $data[][] is an HTML color code
//
function show_image($data, $nx, $ny, $scale) {
    page_head("");
    echo "<table border=1>";

    for ($i=0; $i<$ny; $i++) {
        echo "<tr>";
        for ($j=0; $j<$nx; $j++) {
            $v = $data[$i][$j];
            echo sprintf(
                '<td width=%d height=%d bgcolor="%s"> </td>',
                $scale, $scale, $v
            );
        }
        echo "</tr>\n";
    }
    echo "</table>";
    page_tail();
}

function test() {
    $x = [];
    for ($i=0; $i<180; $i++) {
        $y = [];
        for ($j=0; $j<360; $j++) {
            $r = rand(0,256);
            $c = sprintf("#%02x%02x%02x", $r, $r, $r);
            $y[] = $c;
        }
        $x[] = $y;
    }

    show_image($x, 360, 180, 4);
}

function make_files() {
    $x = json_decode(file_get_contents('coverage.json'));
    $n = count($x);
    $gp = "set terminal pngcairo size 800,600
set style fill transparent solid 0.5 noborder
set style data filledcurves
set xlabel 'RA'
set ylabel 'dec'
plot ";

    $first = true;
    for ($i=0; $i<$n; $i++) {
        $d = $x[$i];
        $dname = $d->name;
        for ($j=0; $j<count($d->modules); $j++) {
            $m = $d->modules[$j];
            $c = $m->c;
            $mname = $m->ser;
            $p0 = $c[0][0];
            $p1 = $c[0][1];
            $p2 = $c[1][0];
            $p3 = $c[1][1];
            $fname = sprintf("mod_%d_%d.dat",$i, $j);
            $f = fopen($fname, "w");
            $s0 = sprintf("%f %f\n", $p0[0]/15, $p0[1]);
            $s1 = sprintf("%f %f\n", $p1[0]/15, $p1[1]);
            $s2 = sprintf("%f %f\n", $p2[0]/15, $p2[1]);
            $s3 = sprintf("%f %f\n", $p3[0]/15, $p3[1]);
            fwrite($f, $s0);
            fwrite($f, $s1);
            fwrite($f, $s3);
            fwrite($f, $s2);
            fwrite($f, $s0);
            fclose($f);
            if ($first) {
                $first = false;
                $gp .= "'$fname' fs solid 0.5 title '$dname $mname' ";
            } else {
                $gp .= ", '$fname' title '$dname $mname' ";
            }
        }
    }
    $gp .= "\n";
    $f = fopen("coverage.gp", "w");
    fwrite($f, $gp);
    fclose($f);
}

make_files();

?>
