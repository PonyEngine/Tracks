//
//  PE_UtilityMethods.h
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/12/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface PE_UtilityMethods : NSObject
+ (NSString *)DistanceOfTimeInWordsfromTime:(int)fromTime toTime:(int)toTime showLessThanAMinute:(bool)showLessThanAMinute;
+ (NSString *) GetDateFromUnixFormat:(NSNumber*) unixFormat;
+(NSNumber *) GetDistancelat1:(double)lat1 lon1:(double)lon1 lat2:(double)lat2 lon2:(double)lon2 inMiles:(bool)inMiles;

+(NSString *)FormatLat:(double )theLat andLon:(double)theLon;
+(UIView *)CreateWaitScreenWithWords:(NSString *)waitWords andSuperView:(UIView *)theSuperView;
+(NSString *)GetServiceHost:(BOOL)withHTTP;
+(UIImageView *)CreateLogoNavBarViewWithBar:(UINavigationBar *)thisNavBar;
+(NSString *) getHumanReadableTimeFromTimeInterval: (NSTimeInterval) theTimeInterval;
+(NSString *) getHumanReadableTimeFromTimeInterval: (NSTimeInterval) theTimeInterval andType:(int)viewType;
+(UIView *)CreateOverlayWithSubView:(UIView *)subView;

//+(BOOL)CheckBoolofJSON:(NSString *)JSONString;
//+(NSNumber *)ParseIntJSON:(NSString *)JSONString;
+ (UIImage *)imageWithImage:(UIImage *)image scaledToSize:(CGSize)newSize;


//Facebook
+(void)PostToMyWallwithTitle:(NSString *)titleName andCaption:(NSString *)caption andLinkURL:(NSString *)linkURL andImageURL:(NSString *)imgURL;

+(void)PostToWallOfFriendIds:(NSArray *)friendIds withTitle:(NSString *)titleName withMessage:(NSString *)message andLink:(NSString *)linkToPage;
+(int)SumArray:(NSArray *)anArray;
+ (void)publishStory:(NSMutableDictionary *)postParams;

@end
