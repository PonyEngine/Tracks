//
//  PE_Profile_ViewController.m
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/23/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import "PE_Profile_ViewController.h"
#import "PE_Manager.h"
#import "UIImageView+AFNetworking.h"

@interface PE_Profile_ViewController ()
   @property (nonatomic,retain) IBOutlet UIImageView *profilePicImageView;
   @property  (nonatomic,retain)IBOutlet UILabel *profileUserNameLbl;
   @property (nonatomic,retain) IBOutlet UILabel *pointsNameLbl;
@end

@implementation PE_Profile_ViewController

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
   
   self.profileUserNameLbl.text=[NSString stringWithFormat:@"%@",[PE_Manager sharedManager].sessionCurrentInfo.fullName];
   ///self.pointsCountLbl.text=[NSString stringWithFormat:@"%@",[PE_Manager sharedManager].sessionCurrentInfo.points];
   [self.profilePicImageView  setImageWithURL:[PE_Manager sharedManager].sessionCurrentInfo.picURL placeholderImage:[UIImage imageNamed:@"defaultuser.png"]];
   self.profilePicImageView.layer.cornerRadius=60.0f;
   self.profilePicImageView.layer.masksToBounds=YES;
   


}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

@end
