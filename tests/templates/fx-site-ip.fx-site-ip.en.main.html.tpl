<? $this->md5_compil='b5512cd0066643bc7e95153d70b1fcfc'; ?>
<!DOCTYPE html>
<html lang="<? if(isset($this->vars['lang']['name'])) echo $this->vars['lang']['name']; ?>">  
  <head>  
    <title>
        <? if ($this->vars['title']  ): ?>
            <? if(isset($this->vars['title'])) echo $this->vars['title']; ?>
        <? else: ?>
            FXTMPartners.com profile
        <? endif; ?>
    </title>
    <link href='/css/fonts.css' rel='stylesheet' type='text/css'>  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="http://fxtmpartners.com/wp-content/themes/pk/img/favicon.png">
    <!-- Bootstrap -->
    <link href="/css/lib/b.css" rel="stylesheet" media="screen">
    <!--[if lt IE 8]>
	  <link href="/css/lib/b_ie7.css" rel="stylesheet" media="screen">
	<![endif]-->	
    <link href="/css/lib/tabulous.css" rel="stylesheet" media="screen">  
    <link href="/css/bootstrap-select.css" rel="stylesheet" media="screen">  
    <link href="/css/sb.css" rel="stylesheet" media="screen">  

	<!-- stylesheets-->
	<? if(isset($this->vars['stylesheets'])) echo $this->vars['stylesheets']; ?>
	<!-- end stylesheets-->
  </head>
  <body>
<? if ($this->vars['has_unaccepted_documents']  ): ?><?=$core->tpl->fetch('documents_popup.html', 1, 0, 0, 'fx-site-ip'); ?><? endif; ?>

        <div class="top-container">
            <div class="header">
                <div class="col-md-12" id="header">
                    <div class="logo">
                        <a href="/<? if(isset($this->vars['lang']['name'])) echo $this->vars['lang']['name']; ?>/"><img src="//my.fxtmpartners.com/accounts/default1/themes/signup/_common_templates/img/logo_pap.png"></a>
                    </div>
                </div>

                <div class="col-md-12 bread"> 
                <div class="row">
                 <div class="col-xs-offset-2 col-md-10 white-links">
                 	<? if ($this->vars['breadcrumbs']  ): ?>
                 		<? if(count($this->vars['breadcrumbs'])) : ?>
						   <? $this->vars['foreach']['breadcrumbs']['count'] = count($this->vars['breadcrumbs']) ?>
						   <? foreach($this->vars['breadcrumbs'] as $this->vars['foreach']['breadcrumbs']['key'] => $this->vars['l']): ?>
						   <? $this->vars['foreach']['key'] = $this->vars['foreach']['breadcrumbs']['key']?>
                 			››&nbsp;<a href="<? if(isset($this->vars['l']['url'])) echo $this->vars['l']['url']; ?>"><? if(isset($this->vars['l']['title'])) echo $this->vars['l']['title']; ?></a>
                 		<? endforeach; ?>
					   <? else:  ?>
					   <!-- empty array : $this->vars['item']['children'] //-->
					   <? endif; ?>
                 	<? endif; ?>
                 	
                    <? if ($this->vars['subheader_menu']  ): ?>
                        <ul class="subheader-links pull-right">
                            <? if(count($this->vars['subheader_menu'])) : ?>
						   <? $this->vars['foreach']['subheader_menu']['count'] = count($this->vars['subheader_menu']) ?>
						   <? foreach($this->vars['subheader_menu'] as $this->vars['foreach']['subheader_menu']['key'] => $this->vars['item']): ?>
						   <? $this->vars['foreach']['key'] = $this->vars['foreach']['subheader_menu']['key']?>
                              <? if ($this->vars['item']['is_granted'] && $this->vars['item']['url']): ?>
                                <li><a href="<? if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>"><? if(isset($this->vars['item']['menu_title'])) echo $this->vars['item']['menu_title']; ?></a></li>
                              <? endif; ?>
                            <? endforeach; ?>
					   <? else:  ?>
					   <!-- empty array : $this->vars['item']['children'] //-->
					   <? endif; ?>
                            <? if ($this->vars['language_selector']  ): ?>
                            <li>
	                            <select name="lang-switcher" id="lang-switcher" class="">
	                            	<? if(count($this->vars['language_selector'])) : ?>
						   <? $this->vars['foreach']['language_selector']['count'] = count($this->vars['language_selector']) ?>
						   <? foreach($this->vars['language_selector'] as $this->vars['foreach']['language_selector']['key'] => $this->vars['ls']): ?>
						   <? $this->vars['foreach']['key'] = $this->vars['foreach']['language_selector']['key']?>
			                    	<option value="<? if(isset($this->vars['ls']['url'])) echo $this->vars['ls']['url']; ?>" <? if ($this->vars['ls']['selected']  ): ?>selected="selected"<? endif; ?>><? if(isset($this->vars['ls']['name'])) echo $this->vars['ls']['name']; ?></option>
			                    	<? endforeach; ?>
					   <? else:  ?>
					   <!-- empty array : $this->vars['item']['children'] //-->
					   <? endif; ?>
			                    </select>
			                </li>
			                <? endif; ?>
                        </ul>
                    <? endif; ?>
                    
                </div>
                </div>
            </div>
            </div>



            <div id="wrap">
                <!-- Sidebar -->
                <div class="nav">
                	<div class="nav-container">
			    <div class="client-id-plate"><? if ($this->vars['client']  ): ?><? if(isset($this->vars['client']['full_name'])) echo $this->vars['client']['full_name']; ?><? endif; ?></div>

	                    <? if ($this->vars['menu']  ): ?>
	                        <ul class="sidebar-nav" id="menu">

	                            <? if(count($this->vars['menu'])) : ?>
						   <? $this->vars['foreach']['menu']['count'] = count($this->vars['menu']) ?>
						   <? foreach($this->vars['menu'] as $this->vars['foreach']['menu']['key'] => $this->vars['item']): ?>
						   <? $this->vars['foreach']['key'] = $this->vars['foreach']['menu']['key']?>
	                            	<? if ($this->vars['item']['is_granted']  ): ?>
	                            		<? if ($this->vars['item']['url'] || $this->vars['item']['children']): ?>
			                                <li class="nav-itm <? if ($this->vars['item']['is_active']  ): ?> active<? endif; ?>">
			                                    <a <? if ($this->vars['item']['is_active']  ): ?>class="active"<? endif; ?> <? if ($this->vars['item']['url']  ): ?>href="<? if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>"<? else: ?>href="#"<? endif; ?>>
			                                        <? if(isset($this->vars['item']['menu_title'])) echo $this->vars['item']['menu_title']; ?>
			                                    </a>
			                                    <? if ($this->vars['item']['children']  ): ?>
			                                        <ul class="collapse">
			                                        <? if(count($this->vars['item']['children'])) : ?>
						   <? $this->vars['foreach']['item.children']['count'] = count($this->vars['item']['children']) ?>
						   <? foreach($this->vars['item']['children'] as $this->vars['foreach']['item.children']['key'] => $this->vars['child']): ?>
						   <? $this->vars['foreach']['key'] = $this->vars['foreach']['item.children']['key']?>
			                                        	<? if ($this->vars['child']['is_granted']  ): ?>
				                                            <li>
				                                                <a <? if ($this->vars['child']['is_active']  ): ?>class="active"<? endif; ?> <? if ($this->vars['child']['url']  ): ?>href="<? if(isset($this->vars['child']['url'])) echo $this->vars['child']['url']; ?>"<? endif; ?>>
				                                                    <? if(isset($this->vars['child']['menu_title'])) echo $this->vars['child']['menu_title']; ?>
				                                                </a>
				                                            </li>
			                                            <? endif; ?>
			                                        <? endforeach; ?>
					   <? else:  ?>
					   <!-- empty array : $this->vars['item']['children'] //-->
					   <? endif; ?>
			                                        </ul>
			                                    <? endif; ?>
			                                </li>
		                                <? endif; ?>
		                            <? endif; ?>
	                            <? endforeach; ?>
					   <? else:  ?>
					   <!-- empty array : $this->vars['item']['children'] //-->
					   <? endif; ?>
	                        </ul>
	                    <? endif; ?>
	                    <? if ($this->vars['client'] && $this->vars['client']['accounts']['summary']['wallet']): ?>
	                    <div class="wallet">
	                    	<h5>My wallet</h5>

	                    	<p>
	                    		BALANCE<br>
	                    		<span class="wallet-balance-amount">$ <? if(isset($this->vars['client']['accounts']['summary']['wallet']['display_balance'])) echo $this->vars['client']['accounts']['summary']['wallet']['display_balance']; ?></span>
	                    	</p>
	                    </div>
	                    <? endif; ?>
	                </div>
                </div>

                <!-- PAGE -->
                <div class="page-container">
                	<div class="">
	                	<div class="col-sm-12">
	                		<? if ($this->vars['index_content']  ): ?>
							    <? if(isset($this->vars['index_content'])) echo $this->vars['index_content']; ?>
							<? else: ?>
							    <? if ($this->vars['header1']  ): ?><h1 class="h-header"><? if(isset($this->vars['header1'])) echo $this->vars['header1']; ?></h1><? endif; ?>
							    <? if(isset($this->vars['content'])) echo $this->vars['content']; ?>
							    <? if(isset($this->vars['controller_content'])) echo $this->vars['controller_content']; ?>
							<? endif; ?>
						</div>
					</div>
<?php if ($this->vars['breadcrumbs'][0]['url'] == '/'.$this->vars['lang']['name'].'/profile/'): ?>
					<div class=" text-muted page-footer">
						<div class="col-md-9 col-sm-8">
					    	<p>
								There is a high level of risk involved with trading leveraged products such as forex and CFDs. You should not risk more than you can afford to lose, it is possible that you may lose more than your initial investment. You should not trade unless you fully understand the true extent of your exposure to the risk of loss. When trading, you must always take into consideration your level of experience. It is the responsibility of the Client to ensure that the Client can accept the Services and/or enter into the Transactions in the country in which the Client is resident. If the risks involved seem unclear to you, please seek independent advice.
							</p>
						</div>
					</div>
<?php endif; ?>
                </div>

            </div>




    </div>
      <script type="text/javascript">
          var lang = '<? if(isset($this->vars['lang']['name'])) echo $this->vars['lang']['name']; ?>';
      </script>
      <!-- eac:compile -->
      <script src="/js/lib/jquery-1.11.1.min.js"></script>
      <script src="/js/lib/b.js"></script>
      <script src="/js/lib/tabulous.min.js"></script>
      <script src="/js/bootstrap-select.min.js"></script>
      <script src="/js/menu.js"></script>  
      <script src="/js/script.js"></script>  
      <!-- /eac:compile -->
<!-- javscripts -->
<? if(isset($this->vars['javascripts'])) echo $this->vars['javascripts']; ?>
<!-- end javascripts-->
  </body>  
</html>