//
//  AFAppPHPClient.m
//  VMote
//
//  Created by Savalas Colbert on 12/15/12.
//  Copyright (c) 2012 Pony Engine. All rights reserved.
//

#import "AFAppPHPClient.h"
#import "AFJSONRequestOperation.h"
#import "PE_Manager.h"

@implementation AFAppPHPClient

+ (AFAppPHPClient *)sharedClient {
    //NSLog(@"%@",[[PE_Manager sharedManager].hostURL absoluteString]);
    
    static AFAppPHPClient *_sharedClient = nil;
    if([ PE_Manager sharedManager].hostChanged){
        _sharedClient = [[AFAppPHPClient alloc] initWithBaseURL:[ PE_Manager sharedManager].hostURL];
    }
    else{
        static dispatch_once_t onceToken;
        dispatch_once(&onceToken, ^{
        _sharedClient = [[AFAppPHPClient alloc] initWithBaseURL:[ PE_Manager sharedManager].hostURL];
        });
    }
    
    return _sharedClient;
}

- (id)initWithBaseURL:(NSURL *)url {
    self = [super initWithBaseURL:url];
    if (!self) {
        return nil;
    }
    
    [self registerHTTPOperationClass:[AFJSONRequestOperation class]];
    
    // Accept HTTP Header; see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
	[self setDefaultHeader:@"Accept" value:@"application/json"];
    
    return self;
}

@end
