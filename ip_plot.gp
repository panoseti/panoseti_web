
        set terminal png size 1000, 1000
        plot "ip_data.tmp" using 1:2 title "value" with lines, "ip_data.tmp" using 1:3 title "mean" with lines
        