<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Logger;

enum Severity: string
{

	case EMERGENCY = 'emergency';
	case ALERT = 'alert';
	case CRITICAL = 'critical';
	case ERROR = 'error';
	case WARNING = 'warning';
	case NOTICE = 'notice';
	case INFO = 'INFO';
	case SUCCESS = 'success';
	case DEBUG = 'debug';

	case STEP = 'step';

}
