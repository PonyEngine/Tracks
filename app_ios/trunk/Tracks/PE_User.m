//
//  PE_User.m
//  VMote
//
//  Created by Savalas Colbert on 12/22/12.
//  Copyright (c) 2012 Pony Engine. All rights reserved.
//

#import "PE_User.h"

@implementation PE_User
@synthesize userId=_userId;
@synthesize usernameEmail=_usernameEmail;
@synthesize firstName=_firstName;
@synthesize fullName=_fullName;
@synthesize profileName=_profileName;
@synthesize picURL=_picURL;
@synthesize tsCreated=_tsCreated;
@synthesize location=_location;
@synthesize bio=_bio;
@synthesize gender=_gender;
@synthesize fbId=_fbId;
@synthesize points=_points;
@synthesize bucks=_bucks;
@synthesize level=_level;
@synthesize following=_following;
@synthesize ubi_id,team_id,custom_scores,response_type,custom_scores_array;//UBI

- (id)initWithJSON:(id)userJSON
    { //Make So this can easily add Core Data
        NSLog(@"%@",userJSON);
        
        self = [super init];
        
        if (self) {
        NSLog(@"The userID is %@",[userJSON valueForKey:@"userId"]);
        //VIDEO
        self.userId=[userJSON valueForKey:@"userId"]!=[NSNull null]?[userJSON valueForKey:@"userId"]:nil;
        self.usernameEmail=[userJSON valueForKey:@"usernameEmail"]!=[NSNull null]?[userJSON valueForKey:@"usernameEmail"]:nil;
        self.profileName=[userJSON valueForKey:@"profileName"]!=[NSNull null]?[userJSON valueForKey:@"profileName"]:nil;
        self.firstName=[userJSON valueForKey:@"firstName"]!=[NSNull null]?[userJSON valueForKey:@"firstName"]:nil;
        self.fullName=[userJSON valueForKey:@"fullName"]!=[NSNull null]?[userJSON valueForKey:@"fullName"]:nil;
        self.location=[userJSON valueForKey:@"location"]!=[NSNull null]?[userJSON valueForKey:@"location"]:nil;
        self.picURL=[userJSON valueForKey:@"picURL"]!=[NSNull null]?[NSURL URLWithString:[userJSON valueForKey:@"picURL"]]:nil;
            
        //NSLog(@"Points are %@",[userJSON valueForKey:@"points"]);
        self.points=[userJSON valueForKey:@"points"]!=[NSNull null]?[userJSON valueForKey:@"points"]:[NSNumber numberWithInt:0];

       // NSLog(@"Bucks are %d",[self.points intValue]);
            
        self.bucks=[userJSON valueForKey:@"bucks"]!=[NSNull null]?[userJSON valueForKey:@"bucks"]:[NSNumber numberWithInt:0];

    
        self.level=[userJSON valueForKey:@"level"]!=[NSNull null]?[userJSON valueForKey:@"level"]:[NSNumber numberWithInt:0];
        
        self.following=[[userJSON valueForKey:@"following"] boolValue];
        //NSLog(@"%@",self.following?@"YES":@"NO");
            
    
            
    //UBI
    self.ubi_id=[userJSON valueForKey:@"ubi_id"]!=[NSNull null]?[userJSON valueForKey:@"ubi_id"]:[NSNumber numberWithInt:0];
    self.team_id=[userJSON valueForKey:@"team_id"]!=[NSNull null]?[userJSON valueForKey:@"team_id"]:[NSNumber numberWithInt:0];
            
   // NSLog(@"Custom Scores is %@",[userJSON valueForKey:@"custom_scores"]);
    self.custom_scores=[userJSON valueForKey:@"custom_scores"]!=[NSNull null]?[userJSON valueForKey:@"custom_scores"]:@",,,,,,,,,,,,,,,,,";
            
    self.response_type=[userJSON valueForKey:@"response_type"]!=[NSNull null]?[userJSON valueForKey:@"response_type"]:nil;
    
            
    }
        
        self.custom_scores_array=[[NSMutableArray alloc] init];
    return self;
}



@end
