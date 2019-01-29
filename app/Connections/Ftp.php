<?php

namespace App\Connections;

use App\Exceptions\Fail;
use App\Helpers\FilenameHelper;
use Gaufrette\Adapter\Ftp as FtpAdapter;
use Gaufrette\Adapter\PhpseclibSftp;
use Gaufrette\Filesystem;
use phpseclib\Net\SFTP;

class Ftp extends Generic
{
    /**
     * @var Filesystem
     */
    private $_filesystem;

    /**
     * SSL constructor.
     *
     * @param array $config
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
        $this->_requiredKeys = ['input' => ['host', 'mode', 'type', 'login', 'password'], 'throttle_period', 'actions', 'enabled', 'uuid'];
        $this->checkConfig();
    }

    /**
     * @throws Fail
     *
     * @return int
     */
    public function exec()
    {
        $this->_filesystem = $this->_getFilesystem();

        $this->_checks();

        return $this->_result;
    }

    /**
     * @throws Fail
     *
     * @return $this
     */
    private function _checks()
    {
        switch ($this->_config['input']['type']) {
            case 'login':
                try {
                    $this->_filesystem->isDirectory('/');
                    $this->_result = true;
                } catch (\Exception $e) {
                    $this->_result = false;
                    throw new Fail($e->getMessage());
                }
                break;
            case 'canWrite':
                try {
                    $name = uniqid('monitoring');
                    $this->_filesystem->write($name, $name);
                    $this->_filesystem->delete($name);
                    $this->_result = true;
                } catch (\Exception $e) {
                    $this->_result = false;
                    throw new Fail($e->getMessage());
                }
                break;
            case 'fileExist':
            case 'fileEmpty':
            case 'fileNotEmpty':
                try {
                    $this->_result = false;
                    $date = new \DateTime();
                    if (! empty($this->_config['input']['interval'])) {
                        $mode = 'sub';
                        if (! empty($this->_config['input']['interval']['mode']) && in_array($this->_config['input']['interval']['mode'], ['add', 'sub'])) {
                            $mode = $this->_config['input']['interval']['mode'];
                        }
                        $date->$mode(new \DateInterval($this->_config['input']['interval']['time']));
                    }

                    $errors = [
                        'fileExist' => ' was not found',
                        'fileEmpty' => ' was not empty',
                        'fileNotEmpty' => ' was empty',
                    ];

                    $size = $this->_filesystem->size(FilenameHelper::dynamic($this->_config['input']['filename'], $date));
                    if ($this->_config['input']['type'] === 'fileExist' && $size >= 0) {
                        $this->_result = true;
                    }
                    if ($this->_config['input']['type'] === 'fileEmpty' && $size === 0) {
                        $this->_result = true;
                    }
                    if ($this->_config['input']['type'] === 'fileNotEmpty' && $size > 0) {
                        $this->_result = true;
                    }

                    if ($this->_result === false) {
                        throw new Fail(FilenameHelper::dynamic($this->_config['input']['filename'], $date) . $errors[$this->_config['input']['type']] . ' (' . $size . ')');
                    }
                } catch (\Exception $e) {
                    $this->_result = false;
                    throw new Fail($e->getMessage());
                }
                break;
        }

        $this->_config['conditions'] = ['boolean' => ['isTrue']];

        return $this;
    }

    /**
     * @throws Fail
     *
     * @return Filesystem
     */
    private function _getFilesystem()
    {
        switch ($this->_config['input']['mode']) {
            case 'sftp':
                if (empty($this->_config['input']['port'])) {
                    $this->_config['input']['port'] = 22;
                }
                $sftp = new SFTP($this->_config['input']['host'], $this->_config['input']['port']);
                if (! $sftp->login($this->_config['input']['login'], decrypt($this->_config['input']['password']))) {
                    throw new Fail('login to ' . $this->_config['input']['host'] . ' fail');
                }
                $adapter = new PhpseclibSftp($sftp, (! empty($this->_config['input']['directory'])) ? $this->_config['input']['directory'] : '/', true);

                return new Filesystem($adapter);
                break;
            case 'ftp':
            case 'ftps':
                if (empty($this->_config['input']['port'])) {
                    $this->_config['input']['port'] = 21;
                }

                $adapter = new FtpAdapter((! empty($this->_config['input']['directory'])) ? $this->_config['input']['directory'] : '/', $this->_config['input']['host'], [
                    'port' => $this->_config['input']['port'],
                    'username' => $this->_config['input']['login'],
                    'password' => $this->_config['input']['password'],
                    'passive' => true,
                    'create' => true,
                    'mode' => FTP_BINARY, // Or FTP_TEXT
                    'ssl' => ($this->_config['input']['mode'] == 'ftps'),
                ]);

                return new Filesystem($adapter);
                break;
        }
    }
}
