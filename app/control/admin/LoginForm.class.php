<?php

class LoginForm extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct($param)
    {
        parent::__construct();
        
        $ini  = AdiantiApplicationConfig::get();
        
        $this->style = 'clear:both';
        // creates the form
        $this->form = new BootstrapFormBuilder('form_login');
        $this->form->setFormTitle( '
        <div style="display: inline-block;float:none; width:100%;padding-top: 10px;">
            <img src="app/images/LogoMacroERP.png" height="160%" width="80%">
        </div>' );
        
        // create the form fields
        $login = new TEntry('login');
        $password = new TPassword('password');
        
        $login->disableAutoComplete();
        $password->disableAutoComplete();
        
        $previous_class = new THidden('previous_class');
        $previous_method = new THidden('previous_method');
        $previous_parameters = new THidden('previous_parameters');
        
        if (!empty($param['previous_class']) && $param['previous_class'] !== 'LoginForm')
        {
            $previous_class->setValue($param['previous_class']);
            
            if (!empty($param['previous_method']))
            {
                $previous_method->setValue($param['previous_method']);
            }
            
            $previous_parameters->setValue(serialize($param));
        }
        
        // define the sizes
        $login->setSize('100%', 40);
        $password->setSize('100%', 40);

        $login->style = 'height:50px; 
        font-size:14px;
        float:left;
        border-bottom-left-radius: 0;
        border-top-left-radius: 0;
        font-family: "Roboto", sans-serif;';

        $password->style = 'height:50px;
        font-size:14px;
        float:left;
        border-bottom-left-radius: 0;
        border-top-left-radius: 0;';

        $redefinir = new THyperLink('Redefinir senha', '../index.php?class=SystemRequestPasswordResetForm', '', 12, 'b');
        $redefinir->style = 'margin-left: 0px';
        
        $login->placeholder = 'Insira seu usuário';
        $password->placeholder = 'Insira sua senha';
        
        $login->autofocus = 'autofocus';

        $this->form->addFields( ['', $login] );
        $this->form->addFields( ['', $password] );

	$this->form->addFields( [$previous_class, $previous_method, $previous_parameters] );
        
        if (!empty($ini['general']['multiunit']) and $ini['general']['multiunit'] == '1')
        {
            $unit_id = new TCombo('unit_id');
            $unit_id->setSize('100%');
            $unit_id->style = '
            height:50px;
            font-size:14px;
            float:left;
            border-bottom-left-radius: 0;
            border-top-left-radius: 0;';
            $unit_id->placeholder = 'Unidade cadastrada';
            $this->form->addFields( ['', $unit_id] );
            $login->setExitAction(new TAction( [$this, 'onExitUser'] ) );
        }
        
        $this->form->addFields([$redefinir]);

        $btn = $this->form->addAction(_t('Log in'), new TAction(array($this, 'onLogin')), '');
        $btn->class = 'btn btn-info btn-lg';
        $btn->style = 'height: 50px;
        width: 90%;
        display: block;
        margin: auto;
        font-size:20px;';
        
        $wrapper = new TElement('div');
        $wrapper->style = '
        margin:auto; 
        margin-top:100px;
        max-width:360px;
        height:468px;
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.54);
        position: relative;
        z-index: 1;
        box-sizing: border-box;';
        $wrapper->id    = 'login-wrapper';
        $wrapper->add($this->form);
        
        // add the form to the page
        parent::add($wrapper);
    }
    
    /**
     * user exit action
     * Populate unit combo
     */
    public static function onExitUser($param)
    {
        try
        {
            TTransaction::open('permission');
            
            $user = SystemUser::newFromLogin( $param['login'] );
            if ($user instanceof SystemUser)
            {
                $units = $user->getSystemUserUnits();
                $options = [];
                
                if ($units)
                {
                    foreach ($units as $unit)
                    {
                        $options[$unit->id] = $unit->unidade;
                    }
                }
                TCombo::reload('form_login', 'unit_id', $options);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error',$e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Authenticate the User
     */
    public static function onLogin($param)
    {
        $ini  = AdiantiApplicationConfig::get();
        
        try
        {
            TSession::regenerate();
            
            $data = (object) $param;
            
            (new TRequiredValidator)->validate( _t('Login'),    $data->login);
            (new TRequiredValidator)->validate( _t('Password'), $data->password);
            
            if (!empty($ini['general']['multiunit']) and $ini['general']['multiunit'] == '1')
            {
                (new TRequiredValidator)->validate( _t('Unit'), $data->unit_id);
            }
            
            if (!empty($ini['general']['require_terms']) && $ini['general']['require_terms'] == '1' && !empty($param['usage_term_policy']) AND empty($data->accept))
            {
                throw new Exception(_t('You need read and agree to the terms of use and privacy policy'));
            }
            
            $user = ApplicationAuthenticationService::authenticate( $data->login, $data->password, false );
            
            if ($user)
            {
                if ( ($form = self::policyTermsVerification($user, $param)) instanceof BootstrapFormBuilder)
                {
                    new TInputDialog(_t('Terms of use and privacy policy'), $form);
                    return;
                }
                
                if ( ($form = self::checkTwoFactor($user, $param)) instanceof BootstrapFormBuilder)
                {
                    new TInputDialog(_t('Two factor authentication'), $form);
                    return;
                }
                
                if (!empty($ini['general']['use_tabs']) && $ini['general']['use_tabs'] == '1')
                {
                    TScript::create("__adianti_clear_tabs()");
                }
                
                if (self::checkForPasswordRenew($user))
                {
                    AdiantiCoreApplication::gotoPage('SystemPasswordRenewalForm');
                    return;
                }
                
                ApplicationAuthenticationService::loadSessionVars($user, true);
                ApplicationAuthenticationService::setUnit( $data->unit_id ?? null );
                ApplicationAuthenticationService::setLang( $data->lang_id ?? null );
                SystemAccessLogService::registerLogin();
                SystemAccessNotificationLogService::registerLogin();
                
                $frontpage = $user->frontpage;
                if (!empty($param['previous_class']) && $param['previous_class'] !== 'LoginForm')
                {
                    AdiantiCoreApplication::gotoPage($param['previous_class'], $param['previous_method'], unserialize($param['previous_parameters'])); // reload
                }
                else if ($frontpage instanceof SystemProgram and $frontpage->controller)
                {
                    AdiantiCoreApplication::gotoPage($frontpage->controller); // reload
                    TSession::setValue('frontpage', $frontpage->controller);
                }
                else
                {
                    AdiantiCoreApplication::gotoPage('EmptyPage'); // reload
                    TSession::setValue('frontpage', 'EmptyPage');
                }
            }
        }
        catch (Exception $e)
        {
            TSession::freeSession();
            new TMessage('error',$e->getMessage());
            sleep(2);
            TTransaction::rollback();
        }
    }
    
    /**
     * Check if password needs to be renewed
     */
    public static function checkForPasswordRenew($user)
    {
        TTransaction::open('permission');
        if (SystemUserOldPassword::needRenewal($user->id))
        {
            TSession::setValue('login', $user->login);
            TSession::setValue('userid', $user->id);
            TSession::setValue('need_renewal_password', true);
            
            return true;
        }
        TTransaction::close();
    }
    
    /**
     * Check 2FA
     */
    public static function checkTwoFactor($user, $param)
    {
        if (!empty($user->otp_secret))
        {
            if (!empty($param['two_factor']))
            {
                $otp = \OTPHP\TOTP::create($user->otp_secret);
                if ($otp->verify($param['two_factor']))
                {
                    return true;
                }
            }
            
            $action = new TAction(['LoginForm', 'onLogin'], $param);
            $form = new BootstrapFormBuilder('two_factor_form');
            
            $two_factor = new TPassword('two_factor');
            $two_factor->style = 'height: 40px;';
            $two_factor->placeholder = _t('Authentication code');
            
            $form->addContent( [ new TLabel(_t('Enter the 6-digit code from your authenticator app')) ] );
            $form->addFields([$two_factor]);
            $form->addFields([new TEntry('lock_enter')])->style = 'display:none';;
            
            $btn = $form->addAction( _t('Authenticate'), $action, '');
            $btn->class = 'btn btn-primary';
            $btn->style = 'height: 40px;width: 90%;display: block;margin: auto;font-size: 17px;';
            
            return $form;
        }
    }
    
    /**
     * Policy terms verification
     */
    private static function policyTermsVerification($user, $param)
    {
        $ini  = AdiantiApplicationConfig::get();
        
        $term_policy = SystemPreference::findInTransaction('permission', 'term_policy');
        
        if (!empty($ini['general']['require_terms']) && $ini['general']['require_terms'] == '1')
        {
            if ($user->accepted_term_policy !== 'Y' && !empty($term_policy) && empty($param['accept']))
            {
                $param['usage_term_policy'] = 'Y';
                $action = new TAction(['LoginForm', 'onLogin'], $param);
                $form = new BootstrapFormBuilder('term_policy');
    
                $content = new TElement('div');
                $content->style = "max-height: 45vh; overflow: auto; margin-bottom: 10px;";
                $content->add($term_policy->value);
    
                $check = new TCheckGroup('accept');
                $check->addItems(['Y' => _t('I have read and agree to the terms of use and privacy policy')]);
    
                $form->addContent([$content]);
                $form->addFields([$check]);
                $form->addAction( _t('Accept'), $action, 'fas:check');
                
                return $form;
            }
            
            if ($user->accepted_term_policy !== 'Y' && !empty($term_policy) && !empty($param['accept']))
            {
                TTransaction::open('permission');
                $user->accepted_term_policy = 'Y';
                $user->accepted_term_policy_at = date('Y-m-d H:i:s');
                $user->accepted_term_policy_data = json_encode($_SERVER);
                $user->store();
                TTransaction::close();
            }
        }
    }
    
    /** 
     * Reload permissions
     */
    public static function reloadPermissions()
    {
        try
        {
            TTransaction::open('permission');
            $user = SystemUser::newFromLogin( TSession::getValue('login') );
            
            if ($user)
            {
                ApplicationAuthenticationService::loadSessionVars($user);
                
                $frontpage = $user->frontpage;
                if ($frontpage instanceof SystemProgram AND $frontpage->controller)
                {
                    TApplication::gotoPage($frontpage->controller); // reload
                }
                else
                {
                    TApplication::gotoPage('EmptyPage'); // reload
                }
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     *
     */
    public function onLoad($param)
    {
    }
    
    /**
     * Logout
     */
    public static function onLogout()
    {
        SystemAccessLogService::registerLogout();
        TSession::freeSession();
        AdiantiCoreApplication::gotoPage('LoginForm', '');
    }
}
