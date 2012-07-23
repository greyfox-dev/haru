<?php
require_once "phing/Task.php";

class DumpTask extends Task
{
	public function main()
	{
		$project = $this->getProject();
		$msg = var_export( $project->getProperties(), true );
		$this->log( $msg );
	}
}