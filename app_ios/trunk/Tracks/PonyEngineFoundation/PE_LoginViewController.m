//
//  PE_LoginViewController.m
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/12/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import "PE_LoginViewController.h"
#import "PEAppDelegate.h"
#import "PE_Manager.h"
#import "AFHTTPClient.h"
#import "AFHTTPRequestOperation.h"
#import "PE_UtilityMethods.h"
#import "AFAppPHPClient.h"
#import "PE_WebViewerViewController.h"
#import "PE_InputCell.h"
#import "PE_ECSVC_InitialSlidingViewController.h"
#import "KeychainItemWrapper.h"

@interface PE_LoginViewController ()
@property (nonatomic,retain) IBOutlet UITableView *loginTableView;
@property (nonatomic,retain) UIView *aWaitScreen;
@property (nonatomic,retain) IBOutlet UIActivityIndicatorView *processingIndicator;
@property (nonatomic,retain) IBOutlet UIView *devModeDots;

-(IBAction)handleLoginWithFacebook:(id)sender;
-(IBAction)viewLoginEmail:(id)sender;
-(IBAction)viewPolicyTerms:(id)sender;
-(IBAction)viewPolicyPrivacy:(id)sender;

@end

@implementation PE_LoginViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
   self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
   if (self) {
      // Custom initialization
   }
   return self;
}


-(void)viewDidLoad{
   [super viewDidLoad];
   
}

-(void)viewDidAppear:(BOOL)animated{
   PEAppDelegate *appDelegate = [UIApplication sharedApplication].delegate;
   
   NSNumber *userId = [appDelegate.userSession objectForKey:(__bridge id)kSecAttrAccount];
   NSString *userAuthTok = [appDelegate.userSession objectForKey:(__bridge id)kSecValueData];
   
   
   // NSString* userId = nil;
   //  NSString* userAuthTok = nil;
   NSLog(@"Retrieving id:%@ and userToken:%@",userId,userAuthTok);
   
   //First Check the userSession from the keyChain
   if ([userId intValue]>0 && [userAuthTok length]>0)
   {
      [PE_Manager sharedManager].sessionCurrentInfo.userId=[userId stringValue];
      [PE_Manager sharedManager].sessionCurrentInfo.userAuthToken=userAuthTok;
      [self goToStartUpView:self];
   }
   //Starts up normally
   
}

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender{
   if ([segue.identifier isEqualToString:@"PE_Segue_ViewPolicyTerms"]){
      PE_WebViewerViewController*aPolicyVC=(PE_WebViewerViewController*)segue.destinationViewController;
      aPolicyVC.uRLAddress=PE_Policy_Terms_Production;
   }
   if ([segue.identifier isEqualToString:@"PE_Segue_ViewPolicyPrivacy"]){
      PE_WebViewerViewController *aPolicyVC=(PE_WebViewerViewController *)segue.destinationViewController;
      aPolicyVC.uRLAddress=PE_Policy_Privacy_Production;
   }
   if ([segue.identifier isEqualToString:@"PE_Segue_ViewLoginEmail"]){
   }
   if ([segue.identifier isEqualToString:@"PE_Segue_LoggedIn"]){
      PE_ECSVC_InitialSlidingViewController *anECSVC=(PE_ECSVC_InitialSlidingViewController *)segue.destinationViewController;
      anECSVC.topViewController = [self.storyboard instantiateViewControllerWithIdentifier:@"TopViewControllerNavigationController"];
      anECSVC.underLeftViewController = [self.storyboard instantiateViewControllerWithIdentifier:@"LeftViewController"];
      anECSVC.underRightViewController = [self.storyboard instantiateViewControllerWithIdentifier:@"RightViewController"];
   }
   
}


- (IBAction)handleLoginWithFacebook:(id)sender {
   self.processingIndicator.hidden=NO;
   [self.processingIndicator startAnimating];
   
   // FBSample logic
   // The user has initiated a login, so call the openSession method.
   PEAppDelegate *appDelegate = [UIApplication sharedApplication].delegate;
   [appDelegate openSessionWithAllowLoginUI:YES];
}

+(void) authenticateUserByFB:(id)sender{
   if (FBSession.activeSession.isOpen) {
      [[FBRequest requestForMe] startWithCompletionHandler:
       ^(FBRequestConnection *connection, NSDictionary<FBGraphUser> *user, NSError *error) {
          if (!error) {
             NSMutableDictionary *params=[[NSMutableDictionary alloc] init];
             if([user objectForKey:@"id"]){
                [params setObject:[user objectForKey:@"id"] forKey:@"fbId"];
                [ PE_Manager sharedManager].sessionCurrentInfo.fbId=[user objectForKey:@"id"];
             }
             
             if([user objectForKey:@"name"]){
                [params setObject:[user objectForKey:@"name"] forKey:@"fbName"];
             }
             
             if([user objectForKey:@"username"])[params setObject:[user objectForKey:@"username"] forKey:@"fbUsername"];
             if([user objectForKey:@"first_name"])[params setObject:[user objectForKey:@"first_name"] forKey:@"fbFName"];
             if([user objectForKey:@"middle_name"])[params setObject:[user objectForKey:@"middle_name"] forKey:@"fbMName"];
             if([user objectForKey:@"last_name"])[params setObject:[user objectForKey:@"last_name"] forKey:@"fbLName"];
             
             if([user objectForKey:@"email"])[params setObject:[user objectForKey:@"email"] forKey:@"fbEmail"];
             
             if([user objectForKey:@"permissions"])[params setObject:[user objectForKey:@"permissions"] forKey:@"fbPermissions"];
             if([user objectForKey:@"link"])[params setObject:[user objectForKey:@"link"] forKey:@"fbProfileURL"];
             
             //Device Info
             UIDevice *currentDevice = [UIDevice currentDevice];
             NSString *model = [currentDevice model];
             NSString *systemVersion = [currentDevice systemVersion];
             
             if(model)[params setObject:[NSNumber numberWithInt:0] forKey:@"device_id"];
             if(systemVersion)[params setObject:systemVersion forKey:@"device_version"];
             if(YES)[params setObject:@"0.0.2"  forKey:@"version"];
             
             //Push Notification Token
             if([PE_Manager sharedManager].sessionCurrentInfo.deviceToken)[params setObject:[PE_Manager sharedManager].sessionCurrentInfo.deviceToken forKey:@"device_token"];
             
             [[AFAppPHPClient sharedClient] getPath:@"fbconnect" parameters:params success:^(AFHTTPRequestOperation *operation, id JSON) {
                NSLog(@"success: %@", operation.responseString);
                NSLog(@"jsonData: %@", JSON);
                
                id data=[JSON valueForKey:@"data"];
                if ([[data valueForKey:@"userIsAuthenticated"]boolValue]==TRUE){
                   [[ PE_Manager sharedManager].sessionCurrentInfo  processCredentials:data];
                   [[PE_Manager sharedManager].sessionCurrentInfo  processProfile:[data valueForKey:@"user"]];
                   
        
                   // [self.view addSubview:self.aWaitScreen];
                   
                   //self.aWaitScreen.hidden=YES;
                   //Report Later
                   
                  // [[PE_Manager sharedManager].sessionCurrentInfo refreshUserProfile:self forViewAppearVC:nil];
                   
                  PEAppDelegate *appDelegate = [UIApplication sharedApplication].delegate;
                  [appDelegate storeSessionData];
                  [appDelegate revealApp:self]; //Could potential place in loading of data
  
                }else{
                   // self.aWaitScreen.hidden=YES;
                   UIAlertView *loginErrorAlert=[[UIAlertView alloc] initWithTitle:@"Could not log you in " message:@"Please check password and try again." delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
                   [loginErrorAlert show];
                   
                   if ([ PE_Manager sharedManager].isDevMode){
                      [[ PE_Manager sharedManager] turnOffDevMode];
                   }
                }
                
             } failure:^(AFHTTPRequestOperation *operation, NSError *error) {
                NSLog(@"error: %@",  operation.responseString);
                
             }];
             
          }
       }];
   }}


- (IBAction)emailLogin:(id)sender{
   NSIndexPath *indexPath0=[NSIndexPath indexPathForRow:0 inSection:0];
   PE_InputCell *userNameInput= (PE_InputCell *)[self.loginTableView cellForRowAtIndexPath:indexPath0];
   
   NSString *usernameEmail=userNameInput.inputTextField.text;
   
   //Get Password String
   NSIndexPath *indexPath1=[NSIndexPath indexPathForRow:1 inSection:0];
   PE_InputCell  *passwordInput=(PE_InputCell *)[self.loginTableView cellForRowAtIndexPath:indexPath1];
   
   NSString *password=passwordInput.inputTextField.text;
   
   //Check if user input data and then process
   if ([usernameEmail length]>0 && [password length]>0) {
      //Activate WaitScreen
      self.aWaitScreen=[PE_UtilityMethods CreateWaitScreenWithWords:@"Logging In" andSuperView:self.view];
      
      NSMutableDictionary *params=[[NSMutableDictionary alloc] init];
      [params setObject:usernameEmail forKey:@"usernameEmail"];
      [params setObject:password forKey:@"password"];
      
      [self.view addSubview:self.aWaitScreen];
      self.aWaitScreen.hidden=NO;
      
      [[AFAppPHPClient sharedClient] getPath:@"authenticate" parameters:params success:^(AFHTTPRequestOperation *operation, id JSON) {
         //[params release];
         NSLog(@"success: %@", operation.responseString);
         NSLog(@"jsonData: %@", JSON);
         
         id data=[JSON valueForKey:@"data"];
         if ([[data valueForKey:@"userIsAuthenticated"]boolValue]==TRUE){
            [[PE_Manager sharedManager].sessionCurrentInfo  processCredentials:data];
            [[PE_Manager sharedManager].sessionCurrentInfo  processProfile:[data valueForKey:@"user"]];

            self.aWaitScreen.hidden=YES;
            //Report Later
            
            if([PE_Manager sharedManager].sessionCurrentInfo.userId>0 && [[PE_Manager sharedManager].sessionCurrentInfo.userAuthToken length]>0)
            {
               PEAppDelegate *appDelegate = [UIApplication sharedApplication].delegate;
               [appDelegate storeSessionData];
               [self goToStartUpView:self];
            }
            
         }else{
            self.aWaitScreen.hidden=YES;
            UIAlertView *loginErrorAlert=[[UIAlertView alloc] initWithTitle:@"Could not log you in " message:@"Please check password and try again." delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
            [loginErrorAlert show];
            //[loginErrorAlert release];
         }
         
      } failure:^(AFHTTPRequestOperation *operation, NSError *error) {
         NSLog(@"error: %@",  operation.responseString);
         
      }];
      
   }else {
      UIAlertView *loginErrorAlert=[[UIAlertView alloc] initWithTitle:@"Required" message:@"A username and password are required" delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
      [loginErrorAlert show];
     // [loginErrorAlert release];
   }
}

- (UITableViewCell *)tableView:(UITableView *)theTableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
   int tablePadding = 10;
   int tableWidth = [_loginTableView frame].size.width;
   if (tableWidth > 480) { // iPad
      tablePadding = 110;
   }
   PE_InputCell *cell;
   if ([indexPath row] == 0) {
      cell = (PE_InputCell *)[_loginTableView dequeueReusableCellWithIdentifier:@"PE_InputCell"];
      if (!cell) {
         cell = [PE_InputCell cell];
      }
      
      [cell layoutSubviewsWithPlaceHolder:@"Profile Name or E-mail" isSecure:NO returnKeyType:UIReturnKeyNext];
      cell.inputTextField.delegate=self;
      
      cell.inputTextField.clearButtonMode=UITextFieldViewModeWhileEditing;
      cell.inputTextField.autocorrectionType=UITextAutocorrectionTypeNo;
      
      
   }else{
      cell =(PE_InputCell *)[_loginTableView dequeueReusableCellWithIdentifier:@"PE_InputCell"];
      if (!cell) {
         cell = [PE_InputCell cell];
      }
      [cell layoutSubviewsWithPlaceHolder:@"Password" isSecure:YES returnKeyType:UIReturnKeyDone];
      cell.inputTextField.delegate=self;
      cell.inputTextField.clearButtonMode=UITextFieldViewModeWhileEditing;
   }
   [cell setSelectionStyle:UITableViewCellSelectionStyleNone];
   return cell;
}

- (NSInteger)tableView:(UITableView *)theTableView numberOfRowsInSection:(NSInteger)section
{
   return 2;
}

- (CGFloat)tableView:(UITableView *)theTableView heightForHeaderInSection:(NSInteger)section
{
   return 34;
}

- (CGFloat)tableView:(UITableView *)theTableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
   return 45;
}

- (NSString *)tableView:(UITableView *)theTableView titleForHeaderInSection:(NSInteger)section
{
   return nil;
}

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
   return 1 ;
}

-(BOOL)textFieldShouldReturn:(UITextField *)textField{
   if ([textField.placeholder isEqualToString:@"Password"]){
      [self emailLogin:self];
   }else{
      NSIndexPath *indexPath0=[NSIndexPath indexPathForRow:1 inSection:0];
      PE_InputCell  *passwordInput=(PE_InputCell *)[self.loginTableView cellForRowAtIndexPath:indexPath0];
      [passwordInput.inputTextField becomeFirstResponder];
   }
   
   return YES;
}

- (void)didReceiveMemoryWarning
{
   [super didReceiveMemoryWarning];
   // Dispose of any resources that can be recreated.
}

-(IBAction)viewPolicyTerms:(id)sender{
      [self performSegueWithIdentifier:@"PE_Segue_ViewPolicyTerms" sender:self];
}

-(IBAction)viewPolicyPrivacy:(id)sender{
   [self performSegueWithIdentifier:@"PE_Segue_ViewPolicyPrivacy" sender:self];
}

-(IBAction)viewLoginEmail:(id)sender{
   [self performSegueWithIdentifier:@"PE_Segue_ViewLoginEmail" sender:self];
}



-(void)goToStartUpView:(id)sender{
   [self performSegueWithIdentifier:@"PE_Segue_LoggedIn" sender:self];
}

-(IBAction)logOut:(UIStoryboardSegue *)segue{
   //Returns to this view and handles all the logout and clean-up
   PEAppDelegate *appDelegate = [UIApplication sharedApplication].delegate;
   [appDelegate clearSessionData];
}
@end
