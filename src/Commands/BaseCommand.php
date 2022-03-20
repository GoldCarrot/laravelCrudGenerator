<?php

namespace Chatway\LaravelCrudGenerator\Commands;

use Illuminate\Console\Command;

abstract class BaseCommand extends Command
{
    protected $signature = '
    {{commandName}}
    {table : Table name in DB}
    {folderNs? : Base namespace folder \App\Domain\{folderNs}\[Entities,repositories]}
    {--def-status-off : Generate Enum Status with default text statuses active, inactive, deleted }
    {--enum= : Generate Enum files, example: ="type-sport,home,work;status-active,inactive,deleted"}
    {--force : Delete and write new files, if off this parameter, then skip files}
    {--previewPaths : View all paths files, generate off }
    {--generateList= : Generate file list, if empty, then generate all files }
    {--action= : Action, example generate - generate files (default); rollback - delete generated files and folders (if empty) }
    ';

    public function __construct()
    {
        parent::__construct();
    }

    protected function setSignature($variables = ['commandName' => 'gen:all'])
    {
        foreach ($variables as $key => $value) {
            $this->signature = str_replace('{{' . $key . '}}', $value, $this->signature);
        }
    }
}
