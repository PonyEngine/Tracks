//
//  PE_ECSVC_TopViewController.m
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/22/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import "PE_ECSVC_TopViewController.h"
#import <QuartzCore/QuartzCore.h>
#import "UIViewController+ECSlidingViewController.h"
#import "PE_ECSVC_LeftViewController.h"
#import "PE_Profile_ViewController.h"

@interface PE_ECSVC_TopViewController () <UITableViewDataSource, UITableViewDelegate, PE_ECSVC_LeftViewControllerDelegate>
   @property (nonatomic, weak) IBOutlet UITableView *tableView;
   @property (nonatomic, strong) NSArray *toDoCategories;
   @property (nonatomic, assign) NSInteger selectedCategory;

   -(IBAction)handleNextBtnTap:(id)sender;
@end


@implementation PE_ECSVC_TopViewController

- (void)viewDidLoad
{
   [super viewDidLoad];
	
   NSDictionary *appsDictionary = @{@"title": @"Apps",
                                      @"items": @[@"FamilyHeartBeat", @"Bet-On-It", @"Spontt"]};
   NSDictionary *workDictionary = @{@"title": @"Work",
                                    @"items": @[@"TPS Report"]};
   NSDictionary *groceryDictionary = @{@"title": @"Grocery List",
                                       @"items": @[@"Chips", @"Salsa", @"Fruit snacks", @"Beer"]};
   
   self.toDoCategories = @[appsDictionary, workDictionary, groceryDictionary];
   self.selectedCategory = 0;
   
   
   //Set-Up Navigation
   self.title=@"Top";
   self.navigationItem.leftBarButtonItem=[[UIBarButtonItem alloc] initWithTitle:@"Left" style:UIBarButtonItemStyleBordered target:self action:@selector(viewUnderLeft:)];
   self.navigationItem.rightBarButtonItem=[[UIBarButtonItem alloc] initWithTitle:@"Right" style:UIBarButtonItemStyleBordered target:self action:@selector(viewUnderRight:)];

   //Add Profile
   PE_Profile_ViewController *aPEProfileVC=[[PE_Profile_ViewController alloc] init];
   aPEProfileVC.view.frame=CGRectMake(self.view.frame.origin.x, self.view.frame.origin.y+70.0f,aPEProfileVC.view.frame.size.width, aPEProfileVC.view.frame.size.height);
   [self.view addSubview:aPEProfileVC.view];

}

- (void)viewWillAppear:(BOOL)animated
{
   [super viewWillAppear:animated];
   
   // Add a shadow to the top view so it looks like it is on top of the others
   self.view.layer.shadowOpacity = 0.75f;
   self.view.layer.shadowRadius = 10.0f;
   self.view.layer.shadowColor = [[UIColor blackColor] CGColor];
   
   // Tell it which view should be created under left
   if (![self.slidingViewController.underLeftViewController isKindOfClass:[PE_ECSVC_LeftViewController class]]) {
      self.slidingViewController.underLeftViewController = [self.storyboard instantiateViewControllerWithIdentifier:@"MenuView"];
      [(PE_ECSVC_LeftViewController *)self.slidingViewController.underLeftViewController setCategoryList:self.toDoCategories];
      [(PE_ECSVC_LeftViewController *)self.slidingViewController.underLeftViewController setDelegate:self];
   }
   
   // Add the pan gesture to allow sliding
   [self.view addGestureRecognizer:self.slidingViewController.panGesture];
}

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender{
   if ([segue.identifier isEqualToString:@"PE_Segue_Next"]){
   }

}


#pragma mark - Tableview DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
   NSDictionary *currentCategory = self.toDoCategories[self.selectedCategory];
   return [currentCategory[@"items"] count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
   static NSString *CellIdentifier = @"Cell";
   UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier forIndexPath:indexPath];
   
   NSDictionary *currentCategory = self.toDoCategories[self.selectedCategory];
   cell.textLabel.text = currentCategory[@"items"][indexPath.row];
   
   return cell;
}

#pragma mark - PE_ECSVC_LeftViewController.m Delegate

- (void)menuViewControllerDidFinishWithCategoryId:(NSInteger)categoryId
{
   self.selectedCategory = categoryId;
   [self.tableView reloadData];
   [self.slidingViewController resetTopViewAnimated:YES];
}

-(IBAction)handleNextBtnTap:(id)sender{
   [self performSegueWithIdentifier:@"PE_Segue_Next" sender:self];
}

-(void)viewUnderLeft:(id)sender{
   if (self.slidingViewController.currentTopViewPosition==ECSlidingViewControllerTopViewPositionAnchoredRight) {
         [self.slidingViewController resetTopViewAnimated:YES onComplete:^{}];
   }else{
      [self.slidingViewController anchorTopViewToRightAnimated:YES onComplete:^{}];
   }
}

-(void)viewUnderRight:(id)sender{
   if (self.slidingViewController.currentTopViewPosition==ECSlidingViewControllerTopViewPositionAnchoredLeft) {
      [self.slidingViewController resetTopViewAnimated:YES onComplete:^{} ];
   }else{
      [self.slidingViewController anchorTopViewToLeftAnimated:YES onComplete:^{}];
   }
}

@end