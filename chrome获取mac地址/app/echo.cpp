#include <stdio.h>
#include <windows.h>
#include <string>
#include <Assert.h>
#include <iphlpapi.h>
#include <stdio.h>
#include <stdlib.h>
#include <cstdlib>
#include <unistd.h>
#include <iostream>


#define WIN32_LEAN_AND_MEAN // Exclude rarely-used stuff from Windows headers


static std::string GetMACaddress(void)
{
IP_ADAPTER_INFO AdapterInfo[16];
// Allocate information for up to 16 NICs
DWORD dwBufLen = sizeof(AdapterInfo);
// Save the memory size of buffer


DWORD dwStatus = GetAdaptersInfo(
// Call GetAdapterInfo
AdapterInfo,  // [out] buffer to receive data
&dwBufLen);  // [in] size of receive data buffer
assert(dwStatus == ERROR_SUCCESS);
// Verify return value is valid, no buffer overflow


PIP_ADAPTER_INFO pAdapterInfo = AdapterInfo;// Contains pointer to current adapter info
std::string outMsg = "";
do {
char acMAC[32];
sprintf(acMAC, "%02X-%02X-%02X-%02X-%02X-%02X",
int (pAdapterInfo->Address[0]),
int (pAdapterInfo->Address[1]),
int (pAdapterInfo->Address[2]),
int (pAdapterInfo->Address[3]),
int (pAdapterInfo->Address[4]),
int (pAdapterInfo->Address[5]));
outMsg.append(acMAC);
outMsg.append(",");
pAdapterInfo = pAdapterInfo->Next;
// Progress through linked list
}
while(pAdapterInfo);
if(outMsg.length()>5){
outMsg = outMsg.substr(0,outMsg.length()-1);
}
    return outMsg;
}


int main(int argc, _TCHAR* argv[]){
 std::string outMsg = "{\"text\":\""+GetMACaddress()+"\"}";
 unsigned int outLen = outMsg.length();
 char *bOutLen = reinterpret_cast<char *>(&outLen);
 std::cout.write(bOutLen, 4);
 std::cout << outMsg << std::flush;
 return 0;
}