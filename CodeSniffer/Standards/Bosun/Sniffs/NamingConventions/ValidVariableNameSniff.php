<?php
/**
 * Bosun_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff', true) === false) {
    $error = 'Class PHP_CodeSniffer_Standards_AbstractVariableSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Bosun_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * Checks the naming of member variables.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 2.8.1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Bosun_Sniffs_NamingConventions_ValidVariableNameSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{


    /**
     * Processes class member variables.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    protected function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $memberProps = $phpcsFile->getMemberProperties($stackPtr);
        if (empty($memberProps) === true) {
            return;
        }

        $memberName     = ltrim($tokens[$stackPtr]['content'], '$');
        $scope          = $memberProps['scope'];
        $scopeSpecified = $memberProps['scope_specified'];

        if ($memberProps['scope'] === 'private') {
            $isPublic = false;
        } else {
            $isPublic = true;
        }

        // If it's a private member, it must have an underscore on the front.
        if ($isPublic === false && $memberName{0} === '_') {
            $error = 'Private member variable "%s" not be prefixed with an underscore';
            $data  = array($memberName);
            $phpcsFile->addError($error, $stackPtr, 'PrivateUnderscore', $data);
            return;
        }

        // If it's not a private member, it must not have an underscore on the front.
        if ($isPublic === true && $scopeSpecified === true && $memberName{0} === '_') {
            $error = '%s member variable "%s" must not be prefixed with an underscore';
            $data  = array(
                      ucfirst($scope),
                      $memberName,
                     );
            $phpcsFile->addError($error, $stackPtr, 'PublicUnderscore', $data);
            return;
        }

    }//end processMemberVar()


    /**
     * Processes normal variables.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
		$tokens = $phpcsFile->getTokens();
		$variableName = ltrim($tokens[$stackPtr]['content'], '$');
		if (false === $this->checkVariable($variableName)) {
			$error = "Variable name '{$variableName}' format must not be camelCaps";
			$data  = array($variableName);
			$phpcsFile->addError($error, $stackPtr, 'VariableNameNoUnderscores', $data);
		}
		//新增限制使用$this->model()方式
		if ($variableName == 'this') {
			$check_content = $variableName;
			for($i=1;$i<4;$i++){
				$stackPtr ++;
				$check_content .= $tokens[$stackPtr]['content'];
			}
			if ($check_content == 'this->model('){
				$error = "Variable name '{$variableName}' format must not be user this->model";
				$data  = array($variableName);
				$phpcsFile->addWarning($error, $stackPtr, 'VariableNameNoUnderscores', $data);
			}
		}
		return;
    }//end processVariable()


    /**
     * Processes variables in double quoted strings.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        /*
            We don't care about normal variables.
        */
		$tokens = $phpcsFile->getTokens();
		$string = $tokens[$stackPtr]['content'];
		if (preg_match_all('/\$[_|\w]+[\s|}]/', $string, $strings, PREG_OFFSET_CAPTURE)) {
			foreach ($strings[0] as $strVar) {
				$variableName = rtrim(ltrim($strVar[0], '$'), '}');
				if (false === $this->checkVariable($variableName)) {
					$error = "Variable name '{$strVar[0]}' format must not be camelCaps as pos {$strVar[1]}";
					$data  = array($strVar[0]);
					$phpcsFile->addError($error, $stackPtr, 'VariableNameNoUnderscores', $data);
				}
			}
		}
		return;

    }//end processVariableInString()

	/**
     * check variables.
     *
     * @param string $variableName variable name.
     *
     * @return boolean
     */
	 protected function checkVariable($variableName)
	{
		//全局变量及Cola框架定义的保护变量
		$filterVariable = ['GLOBALS','_SERVER','_REQUEST','_POST','_GET','_FILES','_ENV','_COOKIE','_SESSION', '_pk', '_table', '_db', '_cache', '_ttl', '_cacheKey', '_validate'];
		//新增限制变量名非驼峰
		if(!in_array($variableName,$filterVariable)){
			if ($variableName !== strtolower($variableName) || $variableName{0} === '_' || substr($variableName,-1) == '_') {
				return false;
			}
		}
		return true;
	}


}//end class
