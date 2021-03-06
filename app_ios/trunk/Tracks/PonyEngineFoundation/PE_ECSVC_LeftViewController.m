//
//  PE_ECSVC_LeftViewController.m
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/22/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import "PE_ECSVC_LeftViewController.h"
#import "UIViewController+ECSlidingViewController.h"

@interface PE_ECSVC_LeftViewController() <UITableViewDelegate, UITableViewDataSource>
   @property (nonatomic, weak) IBOutlet UINavigationBar *navigationBar;
@end

@implementation PE_ECSVC_LeftViewController

- (void)viewDidLoad
{
   [super viewDidLoad];

   [self.slidingViewController setAnchorRightRevealAmount:280.0f];
   //self.slidingViewController.underLeftWidthLayout = ECFullWidth;
   
   self.categoryList = @[];
   [self.navigationBar setBackgroundImage:[UIImage imageNamed:@"nav-bar"] forBarMetrics:UIBarMetricsDefault];
}

#pragma mark - Tableview DataSource

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
   return [self.categoryList count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
   static NSString *CellIdentifier = @"Cell";
   UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier forIndexPath:indexPath];
   
   NSDictionary *currentCategory = self.categoryList[indexPath.row];
   cell.textLabel.text = currentCategory[@"title"];
   
   return cell;
}

#pragma mark - Tableview Delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
   [self.delegate menuViewControllerDidFinishWithCategoryId:indexPath.row];
}

@end

