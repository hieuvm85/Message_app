
import React, { useEffect, useState ,useRef } from 'react';
import axios from 'axios';
import Pusher from 'pusher-js';
import './Group.css'
import { Navigate,useNavigate,useParams  } from 'react-router-dom';

function Group() {

  const token = localStorage.getItem('token');
  const { id } = useParams();
   
  const [user, setUser] = useState({});
  const [group,setGroup]=useState();
  const navigate = useNavigate();
  const [messages,setMessages] = useState()
  const [dataMessages,setDataMessages] = useState([])
  const messagesEndRef = useRef(null);
  const [content, setContent] = useState('');

  const initializePusher = (id) => {
    const pusher = new Pusher('62f650b94e55b6d8db34', {
      cluster: 'ap1',
    });

    const channel = pusher.subscribe('group-' + id);
    channel.bind('sendMessage', (data) => {
      setDataMessages((prev) => [...prev, data.message]);
    });

    // Cleanup function to unbind and unsubscribe
    return () => {
      channel.unbind_all();
      channel.unsubscribe();
    };
  };

  useEffect(() => {
    const fetchUserData = async () => {
      const token = localStorage.getItem('token');
      if (token) {
        try {
          const response = await axios.get('http://localhost:8000/api/user', {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });
          setUser(response.data);

          // Gọi hàm Pusher sau khi đã có id và các dữ liệu cần thiết
          const cleanupPusher = initializePusher(id);
          return cleanupPusher;
        } catch (error) {
          console.log('Error fetching user data:', error);
          // navigate('/login');
        }
      }
    };

    fetchUserData();
  }, []);

  useEffect(() => {
    const fetchUserData = async () => {
      if (token ) {
        try{
          const response =await axios.get(`http://localhost:8000/api/group/${id}`, {
            headers: {  
              Authorization: `Bearer ${token}`
            }
          }); 
          setGroup(response.data.group);
          setMessages(response.data.messages);
          setDataMessages(response.data.messages.data);
          // console.log(response.data);
          console.log(messages);
        }
        catch(error){
          console.log(error);
        }
      }
    }
    fetchUserData();
  },[]); //




  useEffect(() => {
      // Cuộn đến cuối khi component được render
      messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [dataMessages]); // Chỉ cần theo dõi khi messages thay đổi




  const handleOnClickSend = () => {
      setContent('');
      const sendMessage = async () => {
        try {
          // Gửi yêu cầu đến server để lưu tin nhắn

          axios.post(`http://localhost:8000/api/message`, {
            headers: {  
              Authorization: `Bearer ${token}`,
              contentType: 'application/json'
            },


            group_id: id,
            sender_id: user.id,
            content: content ,

          }); 
            // Xử lý sau khi gửi thành công, ví dụ: cập nhật trạng thái tin nhắn
            
        } catch (error) {
            console.error('Error sending message:', error);
        }
      }
      sendMessage();
  };


  return (
    <div> 
      {!group ?"":
      <div className='group-body'>
          <div className='group-chat-container'>
            <div className='group-header-container' >   
                <div className='btn' style={{height:'60px',width:'60px', fontWeight:'700',fontSize:'30px', color:'aqua',display:'flex', justifyContent:'center', alignItems:'center' }} onClick={()=>navigate('/')}>⬅</div>
                <div className='btn' style={{height:'60px', position:'relative',display:'flex', justifyContent:'left', alignItems:'center' }}>
                    <div style={{width:'40px',height:'40px', position:'relative', marginLeft:'5px'}} >
                                <img style={{width:'100%',height:'100%',borderRadius: '50%' }} src="https://inkythuatso.com/uploads/thumbnails/800/2023/03/9-anh-dai-dien-trang-inkythuatso-03-15-27-03.jpg" ></img>
                    </div >
                    <div style={{width:'250px',maxWidth:'250px',height:'40px', position:'relative', marginLeft:'10px',alignContent:'center',fontSize:'16px', fontWeight:'600' , overflow:'hidden',whiteSpace: 'nowrap',textOverflow:'ellipsis'}}>
                      {group.users.length > 2 ? group.name:(group.users[0].id==user.id?group.users[1].name:group.users[0].name)}
                    </div>
                </div>
            </div>
            <div className='group-body-container'>
              <div className='group-body-message-container'>
                {dataMessages.map((message) => (
                      <div
                          key={message.id}
                          className={`message ${message.sender_id == user.id ? 'sent' : 'received'}`}
                      >
                          {message.content}
                      </div>
                  ))}
                   <div ref={messagesEndRef} />
                </ div >
              <div className='group-body-input-container'>
                <textarea
                      class="input-field"
                      value={content}
                      placeholder="Nhập tin nhắn..."
                      onChange={(e) => setContent(e.target.value)}
                  ></textarea>
                <button disabled = {content==''?true:false}  onClick={()=>handleOnClickSend()} class="send-button">Gửi</button>
                </div>
            </div>
          </div> 
      </div>
      } 
    </div>
  );
}

export default Group;
