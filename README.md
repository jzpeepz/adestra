## Installation

Install via Composer:

`composer require jzpeepz/adestra`

Include the service provider in your `config/app.php`:

`Jzpeepz\Adestra\AdestraServiceProvider::class`

Publish the Adestra config file:

`php artisan vendor:publish --tag=adestra`

## Configuration

Below are descriptions of the configuration values found in the the adestra.php config file.

`account` This is the account name found on the login screen.

`username` This is your username.

`password` This is your password.

`core_table_id` This is the id of the core table where your contacts are stored.

`list_id` Optional. This is just a nice place to store your main list id.

## Contacts

### Subscribe a contact to a list

	$list_id = 15;
	$contact = AdestraContact::make(['email' => 'me@emailplace.com']);
	$result = $contact->subscribe($list_id);
	
### Unsubscribe a contact from a list

	$list_id = 15;
	$contact = AdestraContact::make(['email' => 'me@emailplace.com']);
	$result = $contact->unsubscribe($list_id);
	
### Get a contact's list ids

Returns an array of ids of the lists a contact is on.

	$lists = AdestraContact::make(['email' => 'me@emailplace.com'])
				->lists();
	
## Campaigns

### Create a new campaign
	
	$campaign = AdestraCampaign::make([
					'name' => 'My Campaign',
					'description' => 'My New Campaign',
					'project_id' => 31,
					'list_id' => $list_id,
				])
				->create();
				
### Find an existing campaign

	$campaign = AdestraCampaign::find($campaign_id);
	
### Update an existing campaign

	$campaign = $campaign->update([
					'name' => 'My Campaign',
					'description' => 'My New Campaign',
					'project_id' => 31,
					'list_id' => $list_id,
				]);
				
### Set campaign options

	$campaign->setAllOptions([
					'subject_line' => 'My Subject',
					'domain' => 'email.mydomain.com',
					'from_prefix' => 'mail',
					'from_name' => 'My Company',
					'auto_tracking' => 1,
					'user_from' => 1,
					'from_address' => 'me@emailplace.com',
				]);
				
### Set campaign HTML and text

HTML: `$campaign->setMessage('html', $html);`

Text: `$campaign->setMessage('text', $text);`

### Publish a campaign

	$campaign->publish();
	
### Send a test of a campaign

	$campaign->sendTest('me@emailplace.com');
	
This also allows for a comma separated list:

	$campaign->sendTest('me@emailplace.com,you@emailplace.com');
	
### Launch a campaign

	$campaign->launch([
					'launch_label' => 'My Launch',
					'date_scheduled' => date('c'),
				]);