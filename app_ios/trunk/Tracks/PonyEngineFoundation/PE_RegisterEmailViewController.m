//
//  PE_RegisterEmailViewController.m
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/21/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import "PE_RegisterEmailViewController.h"
#import "PEAppDelegate.h"
#import "PE_Manager.h"
#import "AFHTTPClient.h"
#import "AFHTTPRequestOperation.h"
#import "AFJSONRequestOperation.h"
#import "PE_UtilityMethods.h"
#import "PE_InputCell.h"
#import "AFAppPHPClient.h"
#import "PE_WebViewerViewController.h"
#import "PE_ECSVC_InitialSlidingViewController.h"

@interface PE_RegisterEmailViewController ()
@property (nonatomic,retain) IBOutlet UITableView *fullname_emailTableView;
@property (nonatomic,retain) IBOutlet UITableView *passwordUsernamePhoneTableView;
@property (nonatomic,retain) UIView *aWaitScreen;

//Profile Photo
@property (nonatomic,retain) IBOutlet UIButton *profileImageButton;
@property (nonatomic,retain) IBOutlet UIImageView *profileImageView;
@property (nonatomic,retain) UIActionSheet *choosePicActionSheet;
@property (nonatomic,retain) UIImagePickerController *imgPicker;
@property (nonatomic,retain) UIImage *profileImage;
@property (nonatomic,retain) IBOutlet UIScrollView *registerScrollView;
@property (nonatomic,retain) UIView *inputAccView;
@property (nonatomic,retain) UIButton *btnDismiss;

@property (nonatomic,retain) PE_InputCell *fullNameInput;
@property (nonatomic,retain) PE_InputCell *emailInput;
@property (nonatomic,retain) PE_InputCell *passwordInput;
@property (nonatomic,retain) PE_InputCell *usernameInput;
@property (nonatomic,retain) PE_InputCell *phoneInput;
@property (nonatomic,retain) NSMutableDictionary *params;

-(IBAction)registerWithEmail:(id)sender;
-(IBAction)viewPolicyTerms:(id)sender;
-(IBAction)viewPolicyPrivacy:(id)sender;
-(IBAction)cancel:(id)sender;
-(void)goToStartUpView:(id)sender;
@end


@implementation PE_RegisterEmailViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
   self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
   if (self) {
      // Custom initialization

   }
   return self;
}

- (id)initWithCoder:(NSCoder *)aDecoder {
   if(self = [super initWithCoder:aDecoder]) {
      self.profileImage=nil;
      self.params=[[NSMutableDictionary alloc] init];
   }
   
   return self;
}

- (void)viewDidLoad
{
   [super viewDidLoad];
   // Do any additional setup after loading the view from its nib.
   // [[UIApplication sharedApplication] setStatusBarHidden:YES animated:NO];
   
   // Calculate content size given contents
   CGRect contentRect = CGRectZero;
   for ( UIView *subview in self.registerScrollView.subviews ) {
      contentRect = CGRectUnion(contentRect, subview.frame);
   }
   self.registerScrollView.contentSize = CGSizeMake(self.registerScrollView.bounds.size.width, CGRectGetMaxY(contentRect)+10);
   
   // [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillShow:) name:UIKeyboardWillShowNotification object:nil];
   [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillHide:) name:UIKeyboardWillHideNotification object:nil];
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
   
   if ([segue.identifier isEqualToString:@"PE_Segue_LoggedIn"]){
      PE_ECSVC_InitialSlidingViewController *anECSVC=(PE_ECSVC_InitialSlidingViewController *)segue.destinationViewController;
      anECSVC.topViewController = [self.storyboard instantiateViewControllerWithIdentifier:@"TopViewControllerNavigationController"];
      anECSVC.underLeftViewController = [self.storyboard instantiateViewControllerWithIdentifier:@"LeftViewController"];
      anECSVC.underRightViewController = [self.storyboard instantiateViewControllerWithIdentifier:@"RightViewController"];
   }
   
}


-(void)viewWillAppear:(BOOL)animated{
   self.navigationController.navigationBar.barStyle=UIBarStyleBlack;
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}


- (UITableViewCell *)tableView:(UITableView *)theTableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
   
   int tablePadding = 20;
   int tableWidth = [theTableView frame].size.width;
   if (tableWidth > 480) { // iPad
      tablePadding = 110;
   }
   PE_InputCell *cell;
   
   if (theTableView ==self.fullname_emailTableView){
      cell = (PE_InputCell *)[theTableView dequeueReusableCellWithIdentifier:@"PE_InputCell"];
      if (!cell) {
         cell = [PE_InputCell cell];
      }
      if ([indexPath row] == 0) { //Cell 0
         [cell layoutSubviewsWithPlaceHolder:@"Full Name" isSecure:NO returnKeyType:UIReturnKeyNext];
         
         cell.inputTextField.delegate=self;
         cell.inputTextField.clearButtonMode=UITextFieldViewModeWhileEditing;
         cell.inputTextField.autocorrectionType=UITextAutocorrectionTypeNo;
         
         cell.inputTextField.frame=CGRectMake(cell.inputTextField.frame.origin.x, cell.inputTextField.frame.origin.y, 208.0f, cell.inputTextField.frame.size.height);
         
         
      }else{ //Cell 1
         [cell layoutSubviewsWithPlaceHolder:@"Email Address" isSecure:NO returnKeyType:UIReturnKeyNext];
         cell.inputTextField.delegate=self;
         cell.inputTextField.autocapitalizationType=UITextAutocapitalizationTypeNone;
         cell.inputTextField.keyboardType=UIKeyboardTypeEmailAddress;
         cell.inputTextField.clearButtonMode=UITextFieldViewModeWhileEditing;
         
         cell.inputTextField.frame=CGRectMake(cell.inputTextField.frame.origin.x, cell.inputTextField.frame.origin.y, 208.0f, cell.inputTextField.frame.size.height);
      }
   }else { // if (theTableView==self.passwordUsernamePhoneTableView){
      cell = (PE_InputCell *)[theTableView dequeueReusableCellWithIdentifier:@"PE_InputCell"];
      if (!cell) {
         cell = [PE_InputCell cell];
      }
      
      if ([indexPath row] == 0) { //Cell 0
         [cell layoutSubviewsWithPlaceHolder:@"Password" isSecure:YES returnKeyType:UIReturnKeyNext];
         cell.inputTextField.delegate=self;
         cell.inputTextField.autocapitalizationType=UITextAutocapitalizationTypeNone;
         cell.inputTextField.clearButtonMode=UITextFieldViewModeWhileEditing;
         cell.inputTextField.autocorrectionType=UITextAutocorrectionTypeNo;
      }else  if ([indexPath row] == 1){ //Cell 1
         [cell layoutSubviewsWithPlaceHolder:@"Username" isSecure:NO returnKeyType:UIReturnKeyNext];
         cell.inputTextField.delegate=self;
         cell.inputTextField.clearButtonMode=UITextFieldViewModeWhileEditing;
      } else{ //Cell 2
         [cell layoutSubviewsWithPlaceHolder:@"Phone (optional)" isSecure:NO returnKeyType:UIReturnKeyDone];
         cell.inputTextField.delegate=self;
         cell.inputTextField.keyboardType=UIKeyboardTypePhonePad;
         cell.inputTextField.clearButtonMode=UITextFieldViewModeWhileEditing;
      }
      
   }
   [cell setSelectionStyle:UITableViewCellSelectionStyleNone];
   return cell;
}


- (NSInteger)tableView:(UITableView *)theTableView numberOfRowsInSection:(NSInteger)section
{
   return theTableView==self.fullname_emailTableView?2:3;
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
   
   if ([textField.placeholder isEqualToString:@"Full Name"] && [textField isFirstResponder]){
      //Assign new responder
      NSIndexPath *indexPath01=[NSIndexPath indexPathForRow:1 inSection:0];
      PE_InputCell  *emailInput=(PE_InputCell *)[self.fullname_emailTableView cellForRowAtIndexPath:indexPath01];
      [emailInput.inputTextField becomeFirstResponder];
      
   }else if ([textField.placeholder isEqualToString:@"Email Address"]){
      NSIndexPath *indexPath10=[NSIndexPath indexPathForRow:0 inSection:0];
      PE_InputCell  *passwordInput=(PE_InputCell *)[self.passwordUsernamePhoneTableView cellForRowAtIndexPath:indexPath10];
      [passwordInput.inputTextField becomeFirstResponder];
      // CGPoint scrollPoint = CGPointMake(0.0, 100.0f);
      // [self.registerScrollView setContentOffset:scrollPoint animated:YES];
      
   }else if ([textField.placeholder isEqualToString:@"Password"]){
      NSIndexPath *indexPath11=[NSIndexPath indexPathForRow:1 inSection:0];
      PE_InputCell  *usernameInput=(PE_InputCell *)[self.passwordUsernamePhoneTableView cellForRowAtIndexPath:indexPath11];
      [usernameInput.inputTextField becomeFirstResponder];
      // CGPoint scrollPoint = CGPointMake(0.0, 150.0f);
      // [self.registerScrollView setContentOffset:scrollPoint animated:YES];
      
   }else if ([textField.placeholder isEqualToString:@"Username"]){
      
      NSIndexPath *indexPath12=[NSIndexPath indexPathForRow:2 inSection:0];
      PE_InputCell  *phoneInput=(PE_InputCell *)[self.passwordUsernamePhoneTableView cellForRowAtIndexPath:indexPath12];
      [phoneInput.inputTextField becomeFirstResponder];
      //CGPoint scrollPoint = CGPointMake(0.0, 200.0f);
      //  [self.registerScrollView setContentOffset:scrollPoint animated:YES];
      
   }else if ([textField.placeholder isEqualToString:@"Phone (optional)"]){
      [self.phoneInput.inputTextField resignFirstResponder];
      
      //CGPoint scrollPoint = CGPointMake(0.0, 250.0f);
      //[self.registerScrollView setContentOffset:scrollPoint animated:YES];
   }
   
   return YES;
}

-(void)textFieldDidBeginEditing:(UITextField *)textField{
   if ([textField.placeholder isEqualToString:@"Password"]){
      CGPoint scrollPoint = CGPointMake(0.0, 100.0f);
      [self.registerScrollView setContentOffset:scrollPoint animated:YES];
      
   }else  if ([textField.placeholder isEqualToString:@"Username"]){
      CGPoint scrollPoint = CGPointMake(0.0, 150.0f);
      [self.registerScrollView setContentOffset:scrollPoint animated:YES];
      
   } else  if ([textField.placeholder isEqualToString:@"Phone (optional)"]){
      CGPoint scrollPoint = CGPointMake(0.0, 150.0f);
      [self.registerScrollView setContentOffset:scrollPoint animated:YES];
      
   } else  if ([textField.placeholder isEqualToString:@"Email Address"]){
      CGPoint scrollPoint = CGPointMake(0.0, 0.0f);
      [self.registerScrollView setContentOffset:scrollPoint animated:YES];
      
   }
}


- (void)keyboardWillHide:(NSNotification*)notification {
   CGPoint scrollPoint = CGPointMake(0.0, 0.0f);
   [self.registerScrollView setContentOffset:scrollPoint animated:YES];
   
}



-(IBAction)viewPolicyTerms:(id)sender{
   [self performSegueWithIdentifier:@"PE_Segue_ViewPolicyTerms" sender:self];
}

-(IBAction)viewPolicyPrivacy:(id)sender{
   [self performSegueWithIdentifier:@"PE_Segue_ViewPolicyPrivacy" sender:self];
}


-(IBAction)handleProfilePicBtn:(id)sender{
   self.choosePicActionSheet = [[UIActionSheet alloc] initWithTitle:@"Choose or Take Profile Image"
                                                           delegate:self
                                                  cancelButtonTitle:@"Cancel"
                                             destructiveButtonTitle:nil
                                                  otherButtonTitles:@"Take Photo", @"Choose from Library", nil];
   
   // Show the sheet
   [self.choosePicActionSheet showInView:self.view];
}

#pragma mark actionSheet Delegates

- (void)actionSheet:(UIActionSheet *)actionSheet didDismissWithButtonIndex:(NSInteger)buttonIndex
{
   switch (buttonIndex) {
      case 0:
         self.imgPicker = [[UIImagePickerController alloc] init];
         self.imgPicker.delegate =self;
         self.imgPicker.sourceType = UIImagePickerControllerSourceTypeCamera;
         [self presentViewController:self.imgPicker animated:YES completion:nil];
         break;
      case 1:
         self.imgPicker = [[UIImagePickerController alloc] init];
         self.imgPicker.delegate = self;
         self.imgPicker.sourceType = UIImagePickerControllerSourceTypePhotoLibrary;
         [self presentViewController:self.imgPicker animated:YES completion:nil];
         break;
      default:
         //Canceling;
         break;
   }
}

#pragma IMAGE PICKING
- (void) imagePickerController: (UIImagePickerController *) picker
 didFinishPickingMediaWithInfo: (NSDictionary *) info {
   
   UIImage *originalImage, *editedImage, *imageToSave;
   
   // Handle a still image capture
   editedImage = (UIImage *) [info objectForKey:
                              UIImagePickerControllerEditedImage];
   
   originalImage = (UIImage *) [info objectForKey:
                                UIImagePickerControllerOriginalImage];
   
   if (editedImage) {
      imageToSave = editedImage;
   } else {
      imageToSave = originalImage;
   }
   
   // Save the new image (original or edited) to the Button
   
   //[self.profileImageButton setBackgroundImage:imageToSave forState:UIControlStateNormal];
   [self.profileImageView setImage:imageToSave];
   
   //Don't keep creating the image
   //UIImageWriteToSavedPhotosAlbum (imageToSave, nil, nil , nil);
   
   //Save teh profileImageUpright
   CGImageRef imageRef=[imageToSave CGImage];
   
   self.profileImage=[UIImage imageWithCGImage:imageRef scale:1.0 orientation:UIImageOrientationUp];
   
   [picker dismissViewControllerAnimated:YES completion:nil];
}

-(UIView *)inputAccessoryView{
   if(!_inputAccView){
      
      _inputAccView = [[UIView alloc] initWithFrame:CGRectMake(0.0, 0.0, 320.0, 200.0)];
      // Set the view’s background color. We’ ll set it here to gray. Use any color you want.
      [_inputAccView setBackgroundColor:[UIColor clearColor]];
      // We can play a little with transparency as well using the Alpha property. Normally
      // you can leave it unchanged.
      [_inputAccView setAlpha: 1];
      // If you want you may set or change more properties (ex. Font, background image,e.t.c.).
      
      
      _btnDismiss = [UIButton buttonWithType:UIButtonTypeCustom];
      [_btnDismiss setFrame:CGRectMake(0.0f, 0.0f,  320.0, 200.0)];
      //[_btnDismiss setImage:[UIImage imageNamed:@"keyboardDismissBar.png"] forState:UIControlStateNormal];
      // [_btnDismiss setTitle:@"Done" forState:UIControlStateNormal];
      [_btnDismiss setBackgroundColor:[UIColor clearColor]];
      [_btnDismiss setTitleColor:[UIColor clearColor] forState:UIControlStateNormal];
      [_btnDismiss addTarget:self action:@selector(dismissKeyboard:) forControlEvents:UIControlEventTouchUpInside];
      
      // Now that our buttons are ready we just have to add them to our view.
      [_inputAccView addSubview:_btnDismiss];
   }
   return _inputAccView;
}

-(IBAction)dismissKeyboard:(id)sender{
   //resign for all
   
   for (int row=0; row<2; row++) {
      NSIndexPath *indexPath=[NSIndexPath indexPathForRow:row inSection:0];
      PE_InputCell *thePE_InputCell= (PE_InputCell *)[self.fullname_emailTableView cellForRowAtIndexPath:indexPath];
      [thePE_InputCell.inputTextField resignFirstResponder];
   }
   
   for (int row=0; row<3; row++) {
      NSIndexPath *indexPath=[NSIndexPath indexPathForRow:row inSection:0];
      PE_InputCell *thePE_InputCell= (PE_InputCell *)[self.passwordUsernamePhoneTableView cellForRowAtIndexPath:indexPath];
      [thePE_InputCell.inputTextField resignFirstResponder];
   }
   
   
}

-(void)scrollViewDidScroll:(UIScrollView *)scrollView{
   nil;
}

- (IBAction)registerWithEmail:(id)sender{
   
   NSIndexPath *indexPath00=[NSIndexPath indexPathForRow:0 inSection:0];
   NSIndexPath *indexPath10=[NSIndexPath indexPathForRow:1 inSection:0];
   NSIndexPath *indexPath20=[NSIndexPath indexPathForRow:2 inSection:0];
   
   //Full Name
   PE_InputCell *fullNameInput= (PE_InputCell *)[self.fullname_emailTableView cellForRowAtIndexPath:indexPath00];
   NSLog(@"FullName is %@",fullNameInput.inputTextField.text);
   NSString *fullName=fullNameInput.inputTextField.text?fullNameInput.inputTextField.text:@"";
   
   //Email
   PE_InputCell  *emailInput=(PE_InputCell *)[self.fullname_emailTableView cellForRowAtIndexPath:indexPath10];
   NSString *usernameEmail=emailInput.inputTextField.text?emailInput.inputTextField.text:@"";
   
   
   //Password
   PE_InputCell  *passwordInput=(PE_InputCell *)[self.passwordUsernamePhoneTableView cellForRowAtIndexPath:indexPath00];
   NSString *password=passwordInput.inputTextField.text?passwordInput.inputTextField.text:@"";
   
   
   //Username
   PE_InputCell  *usernameInput=(PE_InputCell *)[self.passwordUsernamePhoneTableView cellForRowAtIndexPath:indexPath10];
   NSString *username=usernameInput.inputTextField.text?usernameInput.inputTextField.text:@"";
   
   //Phone
   
   PE_InputCell  *phoneInput=(PE_InputCell *)[self.passwordUsernamePhoneTableView cellForRowAtIndexPath:indexPath20];
   NSString *phone=phoneInput.inputTextField.text?phoneInput.inputTextField.text:@"";
   
   
   if ([fullName length]>0 && [usernameEmail length]>0 && [password length]>0 && [username length]>0) {
      
      [self.params setObject:fullName forKey:@"fullname"];
      [self.params setObject:usernameEmail forKey:@"email"];
      [self.params setObject:password forKey:@"password"];
      [self.params setObject:username forKey:@"username"];
      [self.params setObject:phone forKey:@"phone"];
      
      if(self.profileImage!=nil){
         self.aWaitScreen=[PE_UtilityMethods CreateWaitScreenWithWords:@"Signing Up.." andSuperView:self.view];
         self.aWaitScreen.hidden=NO;
         
         NSData *jpgPhoto = UIImageJPEGRepresentation(self.profileImage, 0.7);
         AFHTTPClient *client= [AFHTTPClient clientWithBaseURL:[PE_Manager sharedManager].hostURL];
         NSMutableURLRequest *afRequest = [client multipartFormRequestWithMethod:@"POST"
                                                                            path:@"register" parameters:self.params
                                                       constructingBodyWithBlock:^(id <AFMultipartFormData>formData) {
                                                          [formData appendPartWithFileData:jpgPhoto
                                                                                      name:@"userPhoto"
                                                                                  fileName:@"photoappfileupload.jpg"
                                                                                  mimeType:@"image/jpeg"];
                                                       }];
         afRequest.timeoutInterval = 10.0;
         AFJSONRequestOperation *operation = [[AFJSONRequestOperation alloc] initWithRequest:afRequest];
         
         [operation setUploadProgressBlock:^(NSInteger bytesWritten, long long totalBytesWritten, long long totalBytesExpectedToWrite) {
            NSLog(@"Sent %lld of %lld bytes", totalBytesWritten, totalBytesExpectedToWrite);
         }];
         
         [operation setCompletionBlock:^{
            id JSON = [(AFJSONRequestOperation *)operation responseJSON];
            NSLog(@"success: %@", operation.responseString);
            NSLog(@"jsonData: %@", JSON);
            
            id data=[JSON valueForKey:@"data"];
            id status=[JSON valueForKey:@"status"];
            if ([[data valueForKey:@"userIsAuthenticated"]boolValue]==TRUE){
               [[PE_Manager sharedManager].sessionCurrentInfo  processCredentials:data];
               
               self.aWaitScreen.hidden=YES;
               //Report Later
               
               if([PE_Manager sharedManager].sessionCurrentInfo.userId>0 && [[PE_Manager sharedManager].sessionCurrentInfo.userAuthToken length]>0){
                  //Reveal
                  PEAppDelegate *appDelegate = [UIApplication sharedApplication].delegate;
                  [appDelegate storeSessionData];
                  [self goToStartUpView:self];
               }
               
            }else{
               self.aWaitScreen.hidden=YES;
               NSString *theErrors=[status valueForKey:@"errors"]?[status valueForKey:@"errors"]:@"Uknown. We are looking into this" ;
               
               UIAlertView *registerErrorAlert=[[UIAlertView alloc] initWithTitle:@"Could not sign you up" message:[NSString stringWithFormat: @"Sorry we were not able to sign you up because %@",theErrors] delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
               [registerErrorAlert show];
            }
            
         }];
         
         
         [operation start];
      }else{
         UIAlertView *anAlertView=[[UIAlertView alloc] initWithTitle:@"No Profile Photo" message:@"Are you sure you want to sign up without a profile photo. Come on. Show your pretty face." delegate:self cancelButtonTitle:@"Go Back" otherButtonTitles:@"I'm Sure",nil];
         [anAlertView show];
      }
   }else{
      NSString *requiredString=@"";
      if([fullName length]==0){requiredString=[requiredString stringByAppendingString:@" Fullname"];}
      
      int lengthofEmail=[usernameEmail length];
      if([usernameEmail length]==0){requiredString=[requiredString length]>0?[requiredString stringByAppendingString:@", email"]:[requiredString stringByAppendingString:@"Email"];}
      
      if([password length]==0){requiredString=[requiredString length]>0?[requiredString stringByAppendingString:@", password"]:[requiredString stringByAppendingString:@"Password"];}
      
      if([username length]==0){requiredString=[requiredString length]>0?[requiredString stringByAppendingString:@", username"]:[requiredString stringByAppendingString:@"Username"];}
      
      
      UIAlertView *anAlertView=[[UIAlertView alloc] initWithTitle:@"Missing Information" message:[NSString  stringWithFormat: @"Required Fields: %@",requiredString] delegate:self cancelButtonTitle:@"All Righty" otherButtonTitles:nil];
      [anAlertView show];
   }
}


-(void) alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex{
   NSString *title=[alertView buttonTitleAtIndex:buttonIndex];
   if ([title isEqualToString:@"I'm Sure"]) {
      if(self.params){
         self.aWaitScreen=[PE_UtilityMethods CreateWaitScreenWithWords:@"Signing Up.." andSuperView:self.view];
         self.aWaitScreen.hidden=NO;
         
         
         NSLog(@"The params %@",self.params);
         [[AFAppPHPClient sharedClient] getPath:@"register" parameters:self.params success:^(AFHTTPRequestOperation *operation, id JSON) {
            NSLog(@"success: %@", operation.responseString);
            NSLog(@"jsonData: %@", JSON);
            
            id data=[JSON valueForKey:@"data"];
            id status=[JSON valueForKey:@"status"];
            if ([[data valueForKey:@"userIsAuthenticated"]boolValue]==TRUE){
               [[PE_Manager sharedManager].sessionCurrentInfo  processCredentials:data];
               
               self.aWaitScreen.hidden=YES;
               //Report Later
               
               if([PE_Manager sharedManager].sessionCurrentInfo.userId>0 && [[PE_Manager sharedManager].sessionCurrentInfo.userAuthToken length]>0){
                  PEAppDelegate *appDelegate = [UIApplication sharedApplication].delegate;
                  [appDelegate storeSessionData];
                  [self goToStartUpView:self];
               }
               
            }else{
               self.aWaitScreen.hidden=YES;
               NSString *theErrors=[status valueForKey:@"errors"]?[status valueForKey:@"errors"]:@"Uknown. We are looking into this" ;
               UIAlertView *loginErrorAlert=[[UIAlertView alloc] initWithTitle:@"Could not sign you up" message:[NSString stringWithFormat: @"Sorry we were not able to sign you up because %@",theErrors] delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
               [loginErrorAlert show];
            }
            
         } failure:^(AFHTTPRequestOperation *operation, NSError *error) {
            NSLog(@"error: %@",  operation.responseString);
            [TestFlight passCheckpoint:@"ERROR_HVC_NOREFRESH"];
            self.aWaitScreen.hidden=YES;
         }];
      }
      
   }
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
