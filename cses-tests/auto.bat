@echo off
setlocal enabledelayedexpansion

for %%f in (*.in) do (
    set "filename=%%~nf"
    set "number=!filename:.in=!"

    mkdir TEST!number!

    copy "!filename!.in" "TEST!number!\input.txt"
    copy "!filename!.out" "TEST!number!\output.txt"
)

endlocal
