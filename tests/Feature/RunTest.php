<?php

it('app it run', function () {
    $this->artisan("run 'C:\Dev\www\cli_calc\ect\example.csv")->assertExitCode(0);
});


it('testExecuteCommandWithPromptedParameter', function () {
    $this->artisan("run 'C:\Dev\www\cli_calc\ect\example.csv")->expectsOutput(getSuccessOutput())->assertExitCode(0);
});

function getSuccessOutput(): string
{
    return "
0.60
3.00
0.00
0.06
1.50
0
0.70
0.30
0.30
3.00
0.00
0.00
8612
";
}



