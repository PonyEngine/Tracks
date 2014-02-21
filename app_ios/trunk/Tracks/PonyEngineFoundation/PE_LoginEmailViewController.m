//
//  PE_LoginEmailViewController.m
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/21/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import "PE_LoginEmailViewController.h"
#import "PE_InputCell.h"
#import "PEAppDelegate.h"
#import "PE_Manager.h"
#import "AFHTTPClient.h"
#import "AFHTTPRequestOperation.h"
#import "PE_UtilityMethods.h"
#import "AFAppPHPClient.h"
#import "PE_InputCell.h"
#import "PE_WebViewerViewController.h"
#import "PE_ECSVC_InitialSlidingViewController.h"

@interface PE_LoginEmailViewController ()
@property (nonatomic,retain) IBOutlet UITableView *tableView;
@property (nonatomic,retain) UIView *aWaitScreen;

//Keyboard Dismiss
@property (nonatomic,retain) UIView *inputAccView;
@property (nonatomic,retain) UIButton *btnDismiss;
-(IBAction)loginWithEmail:(id)sender;
-(IBAction)viewPolicyTerms:(id)sender;
-(IBAction)viewPolicyPrivacy:(id)sender;
-(IBAction)viewRegisterEmail:(id)sender;
-(IBAction)cancel:(id)sender;
-(void)goToStartUpView:(id)sender;
@end

@implementation PE_LoginEmailViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
   [super viewDidLoad];
   // Do any additional setup after loading the view from its nib.
   // [[UIApplication sharedApplication] setStatusBarHidden:YES animated:NO];
   self.navigationItem.rightBarButtonItem=[[UIBarButtonItem alloc] initWithTitle:@"Cancel" style:UIBarButtonItemStyleBordered target:self action:@selector(cancel:)];
   
   self.navigationController.navigationBar.barStyle=UIBarStyleBlack;
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender{
   if ([segue.identifier isEqualToString:@"PE_Segue_ViewPolicyTerms"]){
      PE_WebViewerViewController *aPolicyVC=(PE_WebViewerViewController *)segue.destinationViewController;
      aPolicyVC.uRLAddress=PE_Policy_Terms_Production;
   }
   if ([segue.identifier isEqualToString:@"PE_Segue_ViewPolicyPrivacy"]){
      PE_WebViewerViewController *aPolicyVC=(PE_WebViewerViewController *)segue.destinationViewController;
      aPolicyVC.uRLAddress=PE_Policy_Privacy_Production;
   }
   if ([segue.identifier isEqualToString:@"PE_Segue_ViewRegisterEmail"]){

   }
      if ([segue.identifier isEqualToString:@"PE_Segue_LoggedIn"]){
         PE_ECSVC_InitialSlidingViewController *anECSVC=(PE_ECSVC_InitialSlidingViewController *)segue.destinationViewController;
         anECSVC.topViewController = [self.storyboard instantiateViewControllerWithIdentifier:@"TopViewControllerNavigationController"];
         anECSVC.underLeftViewController = [self.storyboard instantiateViewControllerWithIdentifier:@"LeftViewController"];
         anECSVC.underRightViewController = [self.storyboard instantiateViewControllerWithIdentifier:@"RightViewController"];
      }
   
}


- (IBAction)loginWithEmail:(id)sender{
   NSIndexPath *indexPath0=[NSIndexPath indexPathForRow:0 inSection:0];
   PE_InputCell *userNameInput= (PE_InputCell *)[self.tableView cellForRowAtIndexPath:indexPath0];
   
   NSString *usernameEmail=userNameInput.inputTextField.text;
   
   
   //Get Password String
   NSIndexPath *indexPath1=[NSIndexPath indexPathForRow:1 inSection:0];
   PE_InputCell  *passwordInput=(PE_InputCell *)[self.tableView cellForRowAtIndexPath:indexPath1];
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
         NSLog(@"success: %@", operation.responseString);
         NSLog(@"jsonData: %@", JSON);
         
         id data=[JSON valueForKey:@"data"];
         if ([[data valueForKey:@"userIsAuthenticated"]boolValue]==TRUE){
            [[PE_Manager sharedManager].sessionCurrentInfo  processCredentials:data];
            
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
         }
         
      } failure:^(AFHTTPRequestOperation *operation, NSError *error) {
         NSLog(@"error: %@",  operation.responseString);
         
      }];
      
   }else {
      UIAlertView *loginErrorAlert=[[UIAlertView alloc] initWithTitle:@"Required" message:@"A username and password are required" delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
      [loginErrorAlert show];
   }
}


- (UITableViewCell *)tableView:(UITableView *)theTableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
   int tablePadding = 10;
   int tableWidth = [_tableView frame].size.width;
   if (tableWidth > 480) { // iPad
      tablePadding = 110;
   }
   PE_InputCell *cell;
   if ([indexPath row] == 0) {
      cell = (PE_InputCell *)[_tableView dequeueReusableCellWithIdentifier:@"PE_InputCell"];
      if (!cell) {
         cell = [PE_InputCell cell];
      }
      
      [cell layoutSubviewsWithPlaceHolder:@"Profile Name or E-mail" isSecure:NO returnKeyType:UIReturnKeyNext];
      cell.inputTextField.delegate=self;
      
      cell.inputTextField.clearButtonMode=UITextFieldViewModeWhileEditing;
      cell.inputTextField.autocorrectionType=UITextAutocorrectionTypeNo;
      
      
   }else{
      cell =(PE_InputCell *)[_tableView dequeueReusableCellWithIdentifier:@"PE_InputCell"];
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
   return 0;
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
      [self loginWithEmail:self];
   }else{
      NSIndexPath *indexPath0=[NSIndexPath indexPathForRow:1 inSection:0];
      PE_InputCell  *passwordInput=(PE_InputCell *)[self.tableView cellForRowAtIndexPath:indexPath0];
      [passwordInput.inputTextField becomeFirstResponder];
   }
   
   
   
   return YES;
}



-(IBAction)dismissKeyboard:(id)sender{
   //resign for all
   NSIndexPath *indexPath0=[NSIndexPath indexPathForRow:0 inSection:0];
   PE_InputCell *userNameInput= (PE_InputCell *)[self.tableView cellForRowAtIndexPath:indexPath0];
   [userNameInput.inputTextField resignFirstResponder];
   
   NSIndexPath *indexPath01=[NSIndexPath indexPathForRow:1 inSection:0];
   PE_InputCell *password= (PE_InputCell *)[self.tableView cellForRowAtIndexPath:indexPath01];
   [password.inputTextField resignFirstResponder];
   
}


//Keyboard Dismiss Bar
-(void)textFieldDidBeginEditing:(UITextField *)textField{
   [textField setInputAccessoryView:[self inputAccessoryView]];
   // Set the active field. We' ll need that if we want to move properly
   // between our textfields.
   // txtActiveField = textField;
}

-(UIView *)inputAccessoryView{
   if(!_inputAccView){
      _inputAccView = [[UIView alloc] initWithFrame:CGRectMake(0.0, 0.0, 20.0, 110.0)];
      // Set the view’s background color. We’ ll set it here to gray. Use any color you want.
      [_inputAccView setBackgroundColor:[UIColor clearColor]];
      // We can play a little with transparency as well using the Alpha property. Normally
      // you can leave it unchanged.
      [_inputAccView setAlpha: 1];
      // If you want you may set or change more properties (ex. Font, background image,e.t.c.).
      
      
      _btnDismiss = [UIButton buttonWithType:UIButtonTypeCustom];
      [_btnDismiss setFrame:CGRectMake(0.0f, 0.0f,  320.0, 110.0)];
      //[_btnDismiss setImage:[UIImage imageNamed:@"keyboardDismissBar.png"] forState:UIControlStateNormal];
      //[_btnDismiss setTitle:@"Done" forState:UIControlStateNormal];
      [_btnDismiss setBackgroundColor:[UIColor clearColor]];
      [_btnDismiss setTitleColor:[UIColor clearColor] forState:UIControlStateNormal];
      [_btnDismiss addTarget:self action:@selector(dismissKeyboard:) forControlEvents:UIControlEventTouchUpInside];
      
      // Now that our buttons are ready we just have to add them to our view.
      [_inputAccView addSubview:_btnDismiss];
   }
   return _inputAccView;
}

-(IBAction)viewPolicyTerms:(id)sender{
   [self performSegueWithIdentifier:@"PE_Segue_ViewPolicyTerms" sender:self];
}

-(IBAction)viewPolicyPrivacy:(id)sender{
   [self performSegueWithIdentifier:@"PE_Segue_ViewPolicyPrivacy" sender:self];
}



-(IBAction)viewRegisterEmail:(id)sender{
   //Release the keyboard
   NSIndexPath *indexPath0=[NSIndexPath indexPathForRow:0 inSection:0];
   PE_InputCell *userNameInput= (PE_InputCell *)[self.tableView cellForRowAtIndexPath:indexPath0];
   [userNameInput.inputTextField resignFirstResponder];
   
   //Get Password String
   NSIndexPath *indexPath1=[NSIndexPath indexPathForRow:1 inSection:0];
   PE_InputCell  *passwordInput=(PE_InputCell *)[self.tableView cellForRowAtIndexPath:indexPath1];
   [passwordInput.inputTextField resignFirstResponder];
   
   [self performSegueWithIdentifier:@"PE_Segue_ViewRegisterEmail" sender:self];
}

-(IBAction)cancel:(id)sender{
   [self dismissViewControllerAnimated:YES completion:nil];
}


-(void)goToStartUpView:(id)sender{
   if ([ PE_Manager sharedManager].sessionCurrentInfo.userAuthToken) { //Check of session present
      //Startup view will always be circles with the ability to Create or Join
      [self performSegueWithIdentifier:@"PE_Segue_LoggedIn" sender:self];
   }
}
@end

