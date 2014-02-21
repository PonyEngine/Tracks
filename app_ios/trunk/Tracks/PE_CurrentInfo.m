//
//  PE_CurrentInfo.m
//  VMote
//
//  Created by Savalas Colbert on 12/9/12.
//  Copyright (c) 2012 Pony Engine. All rights reserved.
//

#import "PE_UtilityMethods.h"
#import "PE_CurrentInfo.h"
#import "PE_Manager.h"
#import "PE_User.h"
#import "AFHTTPClient.h"
#import "AFHTTPRequestOperation.h"
#import "AFAppPHPClient.h"

@implementation PE_CurrentInfo
@synthesize userAuthToken=_userAuthToken;
@synthesize role=_role;

//User
@synthesize userId=_userId;
@synthesize usernameEmail=_usernameEmail;
@synthesize userPwd=_userPwd;
@synthesize profileName=_profileName;
@synthesize firstName=_firstName;
@synthesize middleName=_middleName;
@synthesize lastName=_lastName;
@synthesize picURL=_picURL;
@synthesize tsCreated=_tsCreated;
@synthesize tsLastLogin=_tsLastLogin;
@synthesize location=_location;
@synthesize bio=_bio;
@synthesize gender=_gender;
@synthesize fbId=_fbId;
@synthesize bets=_bets;
@synthesize bucks=_bucks;
@synthesize points=_points;
@synthesize level=_level;
@synthesize openInvites=_openInvites;

@synthesize betTotalNFL;
@synthesize betTotalNBA;
@synthesize betTotalMLB;
@synthesize betTotalNHL;
@synthesize deviceToken;
@synthesize pushMsgs;
@synthesize winLosses;
@synthesize isNewMember;
- (id)init
{
    self = [super init];
    if (self) {
        self.deviceToken=nil;
        self.pushMsgs=[[NSMutableArray alloc] init];
        self.winLosses=[[NSMutableArray alloc] init];
    }
    return self;
}

- (void) processCredentials:(id)jsonData
{
    self.userAuthToken=[jsonData valueForKey:@"userAuthToken"]!=[NSNull null]?[jsonData objectForKey:@"userAuthToken"]:@"";
    self.userId=[jsonData valueForKey:@"userId"]!=[NSNull null]?[jsonData valueForKey:@"userId"]:@"";
    self.role=[jsonData valueForKey:@"role"]!=[NSNull null]?[jsonData valueForKey:@"role"]:@"";
    self.tsLastLogin=[jsonData valueForKey:@"tsLastLogin"]!=[NSNull null]?[jsonData valueForKey:@"tsLastLogin"]:[NSNumber numberWithInt:0];
    self.openInvites=[[NSMutableArray alloc] init];
    self.isNewMember=[self.tsLastLogin intValue]>0?NO:YES;
    
   // NSLog(@"%@ %@ %@ %@",self.userAuthToken,self.userId,self.role,self.tsLastLogin);
    
}



-(BOOL)inArrayWithValue:(NSNumber *)needle AndArray:(NSMutableArray*)anArray{
    for (NSNumber *idNum in anArray) {
        if ([idNum integerValue]==[needle integerValue]) return YES;
    }
    return NO;
}

- (NSArray *)processUsers:(NSArray *)users
{
    NSLog(@"%@",users);
    NSMutableArray *theUsers=[[NSMutableArray alloc] init];
    for (NSDictionary *theUser in users) {
        PE_User *aPE_User=[[PE_User alloc] initWithJSON:theUser];
        [theUsers addObject:aPE_User];
    }
    return theUsers;
}

- (void) processProfile:(id)profile
{
    NSLog(@"%@",profile);
    self.role=[profile valueForKey:@"role"]!=[NSNull null]?[profile valueForKey:@"role"]:@"";
    self.usernameEmail=[profile valueForKey:@"usernameEmail"]!=[NSNull null]?[profile valueForKey:@"usernameEmail"]:@"";
    self.profileName=[profile valueForKey:@"profileName"]!=[NSNull null]?[profile valueForKey:@"profileName"]:@"";
    self.firstName=[profile valueForKey:@"firstName"]!=[NSNull null]?[profile valueForKey:@"firstName"]:@"";
    self.middleName=[profile valueForKey:@"middleName"]!=[NSNull null]?[profile valueForKey:@"middleName"]:@"";
    self.lastName=[profile valueForKey:@"lastName"]!=[NSNull null]?[profile valueForKey:@"lastName"]:@"";

    if(!self.picURL){  //Optimization
        NSLog(@"%@",[profile valueForKey:@"picURL"]);
        self.picURL=[profile valueForKey:@"picURL"]!=[NSNull null]?[NSURL URLWithString:[profile valueForKey:@"picURL"]]:@"";
    }
    
    self.tsCreated=[profile valueForKey:@"tsCreated"]!=[NSNull null]?[profile valueForKey:@"tsCreated"]:[NSNumber numberWithInt:0];
    self.location=[profile valueForKey:@"location"]!=[NSNull null]?[profile valueForKey:@"location"]:@"";
    self.bio=[profile valueForKey:@"bio"]!=[NSNull null]?[profile valueForKey:@"bio"]:@"";
    self.gender=[profile valueForKey:@"gender"]!=[NSNull null]?[profile valueForKey:@"gender"]:@"";
    
    //Counts
    self.bets=[profile valueForKey:@"bets"]!=[NSNull null]?[profile valueForKey:@"bets"]:[NSNumber numberWithInt:0];
    self.bucks=[profile valueForKey:@"bucks"]!=[NSNull null]?[profile valueForKey:@"bucks"]:[NSNumber numberWithInt:0];
    
    self.points=[profile valueForKey:@"points"]!=[NSNull null]?[profile valueForKey:@"points"]:[NSNumber numberWithInt:0];
    
    self.level=[profile valueForKey:@"level"]!=[NSNull null]?[profile valueForKey:@"level"] :[NSNumber numberWithInt:0];
    
    
    //Bet Totals
    NSLog(@"Bet Total NFL %@",[profile valueForKey:@"betTotalNFL"]);
    
    self.betTotalNFL=[profile valueForKey:@"betTotalNFL"]!=[NSNull null]?[profile valueForKey:@"betTotalNFL"] :[NSNumber numberWithInt:0];
    
    self.betTotalNBA=[profile valueForKey:@"betTotalNBA"]!=[NSNull null]?[profile valueForKey:@"betTotalNBA"] :[NSNumber numberWithInt:0];
    
    self.betTotalMLB=[profile valueForKey:@"betTotalMLB"]!=[NSNull null]?[profile valueForKey:@"betTotalMLB"] :[NSNumber numberWithInt:0];
    
    self.betTotalNHL=[profile valueForKey:@"betTotalNHL"]!=[NSNull null]?[profile valueForKey:@"betTotalNHL"] :[NSNumber numberWithInt:0];
    
}


-(void)refreshUserProfile:(id)sender forViewAppearVC:(UIViewController *)aViewController{
    //Move to common place
    
    NSMutableDictionary *params=[[NSMutableDictionary alloc] init];
    [params setObject:[ PE_Manager sharedManager].sessionCurrentInfo.userId forKey:@"userId"];
    [params setObject:[ PE_Manager sharedManager].sessionCurrentInfo.userAuthToken  forKey:@"authToken"];
    
    [[AFAppPHPClient sharedClient] getPath:@"userprofilewithcredentials" parameters:params success:^(AFHTTPRequestOperation *operation, id JSON) {
       // [params release];
        NSLog(@"success: %@", operation.responseString);
        NSLog(@"jsonData: %@", JSON);
        
        if ([[[JSON valueForKey:@"status"] valueForKey:@"code"] intValue]==200){
            id data=[JSON valueForKey:@"data"];
            NSLog(@"The Value of user %@",[data valueForKey:@"profile"]);
           [self processProfile:[data valueForKey:@"profile"]];
            
            PEAppDelegate *appDelegate = [UIApplication sharedApplication].delegate;
            
            
            if (aViewController) {
                [aViewController viewWillAppear:NO];
            }else{
                [appDelegate revealApp:self]; //Could potential place in loading of data
            }
            
        }else{
           // [TestFlight passCheckpoint:@"ERROR_HVC_"];
            
        }
        
    } failure:^(AFHTTPRequestOperation *operation, NSError *error) {
        NSLog(@"error: %@",  operation.responseString);
       // [TestFlight passCheckpoint:@"ERROR_HVC_NOREFRESH"];
        
    }];
}


-(NSString *)distanceFromLat:(NSNumber*)lat AndLon:(NSNumber *)lon{
    
    //NSTimeInterval howRecent = [eventDate timeIntervalSinceNow];
    
  //  if (abs(howRecent) < 15.0) {};//Get Recent later- keep generating and hone this
        NSLog(@"%+.6f",[ PE_Manager sharedManager].locationManager.location.coordinate.latitude);

    
   return [NSString stringWithFormat:@"%1.2f miles away",[[PE_UtilityMethods GetDistancelat1:[lat doubleValue]
                               lon1:[lon doubleValue]
                               lat2:[ PE_Manager sharedManager].locationManager.location.coordinate.latitude
                               lon2:[ PE_Manager sharedManager].locationManager.location.coordinate.longitude inMiles:YES] floatValue]];
}
- (void) eraseInfoForUser
{
    self.userId=@"";
    self.fbId=@"";
    self.usernameEmail=@"";
    self.profileName=@"";
    self.firstName=@"";
    self.middleName=@"";
    self.lastName=@"";
    self.gender=@"";
    self.userAuthToken=@"";
    self.role=@"";
    
    //Counts
    self.bucks=nil;
    self.points=nil;
    self.level=nil;
}

-(NSString *)fullName{
    return [NSString stringWithFormat:@"%@ %@",self.firstName,self.lastName];
}


@end
