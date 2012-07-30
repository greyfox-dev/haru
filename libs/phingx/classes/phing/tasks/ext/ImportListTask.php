<?php
require_once 'phing/Task.php';
require_once 'phing/tasks/system/ImportTask.php';

class ImportListTask extends Task
{
	private $_srcFileListArr = array();

	/**
	 * all fileset objects assigned to this task
	 *
	 * @var unknown_type
	 */
	protected $filesets = array();

	/**
	 * all filelist objects assigned to this task
	 *
	 * @var unknown_type
	 */
	protected $filelists = array();

	/**
	 * Nested creator, creates a FileSet for this task
	 *
	 * @access public
	 * @return object The created fileset object
	 */
	function createFileSet()
	{
		$num = array_push( $this->filesets, new FileSet() );
		return $this->filesets[ $num - 1 ];
	}

	/**
	 * Nested creator, adds a set of files (nested fileset attribute).
	 *
	 * @access public
	 * @return object The created filelist object
	 */
	function createFileList()
	{
		$num = array_push( $this->filelists, new FileList() );
		return $this->filelists[ $num - 1 ];
	}

	/**
	 * The init method: Do init steps.
	 */
	public function init()
	{

	}

	public function main()
	{
		$this->_initSrcFileListArr();
		$srcFileList = $this->_srcFileListArr;

		foreach ( $srcFileList as $filename )
		{
			$importTask = new ImportTask();
			$importTask->setProject( $this->getProject() );
			$importTask->setLocation( $this->getLocation() );
			$importTask->setFile( $filename );
			$importTask->main();
		}
	}

	private function _initSrcFileListArr()
	{
		$project = $this->getProject();

		// process filesets
		foreach ( $this->filesets as $fs )
		{
			$ds = $fs->getDirectoryScanner( $project );
			$fromDir = $fs->getDir( $project );
			$srcFiles = $ds->getIncludedFiles();

			foreach ( $srcFiles as $filename )
			{
				$this->_addFile( $fromDir->getAbsolutePath() . '/' . $filename );
			}
		}

		// process filelists
		foreach ( $this->filelists as $fl )
		{
			$fromDir = $fl->getDir( $project );

			$srcFiles = $fl->getFiles( $project );
			$srcDirs = $fl->getDir( $project );

			foreach ( $srcFiles as $filename )
			{
				$this->_addFile( $srcDirs->getAbsolutePath() . '/' . $filename );
			}
		}
	}

	private function _addFile( $filename )
	{
		if ( !in_array( $filename, $this->_srcFileListArr ) && is_file( $filename ) )
		{
			$this->_srcFileListArr[] = $filename;
		}
	}
}