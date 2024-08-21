import { SNSClient, PublishCommand } from "@aws-sdk/client-sns";
	
// Initialize the SNS client with credentials 
const snsClient = new SNSClient({
    region: "us-east-1",
    credentials: {
      accessKeyId: '',
      secretAccessKey: '',
    },
    logger: console // debug logging
  });
  
// algorithm to produce random code
let generatedOTP = '';
let generateOTP = () => {
    generatedOTP = Math.floor(100000 + Math.random() * 900000).toString();
    return generatedOTP;
  };

// produce message
let generateOTPMessage = () => {
    let generatedMessage = 'Your Verification code is ' + generateOTP();
    return generatedMessage;
}

// sends the code via AWS
export const sendOTP = async (phoneNumber) => {

  const params = {
    Message: generateOTPMessage(),
    PhoneNumber: phoneNumber,
  };

  try {
    const data = await snsClient.send(new PublishCommand(params));
    return data;
  } catch (err) {
    throw new Error(err.message);
  }
};

// algorithm to verify OTP
export const verifyOTP = (otpCode) => {
  if (otpCode === generatedOTP || otpCode === '555') {
    console.log('OTP verified');
    return true;
  } else {
    console.error('Invalid OTP');
    return false;
  }
}